const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const { test } = require('node:test');

// Execute the actual browser code without adding a frontend dependency chain.
function cotizador(plan = 'Servicio Especializado', values = {}) {
    const elements = new Map();
    function element(id) {
        if (!elements.has(id)) {
            elements.set(id, {
                value: String(values[id] ?? ''), checked: false, innerText: '', innerHTML: '', style: {},
                addEventListener() {}, querySelector: () => element(`${id}-label`),
            });
        }
        return elements.get(id);
    }
    const requests = [];
    const context = vm.createContext({
        document: {
            getElementById: element,
            addEventListener() {},
            querySelector: selector => selector === 'input[name="plan_integral"]:checked' ? { value: plan } : null,
            querySelectorAll: () => [],
        },
        console, Intl, setTimeout() {}, alert() {}, location: {},
        fetch: async (url, options) => {
            requests.push({ url, payload: JSON.parse(options.body) });
            // Avoid the unrelated success dialogs and automatic file download.
            return { json: async () => ({ status: 'error', message: 'Test response' }) };
        },
    });
    context.window = context;
    vm.runInContext(fs.readFileSync(path.join(__dirname, '../../public/js/custom.js'), 'utf8'), context);
    return { context, element, requests };
}

const specialized = {
    esp_base_100: 30000, esp_neto_100: 25000, esp_carga_100: 9000,
    esp_bruto_for: 20000, esp_neto_for: 17000, esp_carga_for: 6000, comision_porcentaje: 5,
};
const currency = number => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(number);

for (const [name, overrides, total, surplus, commission, savings] of [
    ['gross differs from net', {}, 35250, 8000, 1250, 3750],
    ['no negative surplus; additional investment', { esp_neto_for: 27000, esp_bruto_for: 32000 }, 39250, 0, 1250, -250],
    ['zero commission', { comision_porcentaje: 0 }, 34000, 8000, 0, 5000],
    ['cents', { esp_bruto_for: 20000.25, esp_neto_for: 17000.50 }, 35249.75, 7999.50, 1250, 3750.25],
    ['empty inputs', Object.fromEntries(Object.keys(specialized).map(key => [key, ''])), 0, 0, 0, 0],
]) {
    test(`Specialized screen and PDF option: ${name}`, () => {
        const { context, element } = cotizador('Servicio Especializado', { ...specialized, ...overrides });
        context.calcularEspecializado();
        assert.equal(element('res_esp_for_total').innerText, currency(total));
        assert.equal(element('esp_excedente_val').innerText, currency(surplus));
        assert.equal(Number(element('esp_comision_monto').value), commission);
        assert.equal(element('res_esp_diferencia').innerText, currency(savings));
        assert.equal(element('esp_ahorro_anual_val').innerText, currency(savings * 12));
        context.fijarOpcionActual();
        const option = context.opcionesPropuesta[0].esp;
        assert.equal(option.total_for, total);
        assert.equal(option.excedente, surplus);
        assert.equal(option.comision_monto, commission);
        if (total > 0) {
            context.generarPDFMembretado({});
            const rows = element('pdf_mega_body').innerHTML.split('</tr>');
            assert.ok(rows.find(row => row.includes('Costo Total Mensual')).includes(currency(total)));
            assert.ok(rows.find(row => row.includes('Salarios Brutos')).includes(currency(option.sueldos_for)));
            assert.ok(rows.find(row => row.includes('Ahorro Mensual')).includes(currency(savings)));
        }
    });
}

test('Creating and updating send the same gross, net, surplus and total', async () => {
    const { context, element, requests } = cotizador('Servicio Especializado', specialized);
    context.calcularEspecializado();
    await context.guardarPropuestaBD();
    element('propuesta_id').value = '42';
    await context.actualizarCotizacionExistente();
    assert.equal(requests.length, 2);
    assert.ok(requests[0].url.endsWith('cotizador/guardar'));
    assert.ok(requests[1].url.endsWith('cotizador/actualizar/42'));
    for (const { payload } of requests) {
        assert.equal(payload.esp_bruto_for, 20000);
        assert.equal(payload.esp_neto_for, 17000);
        assert.equal(payload.esp_excedente, 8000);
        assert.equal(payload.esp_comision_monto, 1250);
        assert.equal(payload.esp_total_for, 35250);
        assert.equal(payload.esp_diferencia, 3750);
    }
});

test('Mixed plan preserves PROEM, employee commission and its social charge', () => {
    const { context, element } = cotizador('Mixto', {
        ...specialized,
        base_100: 30000, neto_100: 25000, carga_100: 9000, comision_100: 1000,
        bruto_proem: 20000, neto_proem: 17000, carga_proem: 6000,
        comision_empleado_proem: '',
    });
    element('comision_100_afecta_carga').checked = true;
    context.calcularProemFinal();
    context.calcularEspecializado();
    context.fijarOpcionActual();
    assert.equal(element('comision_empleado_proem').value, '1000.00');
    assert.equal(element('excedente_val').innerText, currency(9000));
    assert.equal(element('total_100_display').innerText, currency(40250));
    assert.equal(element('total_proem_display').innerText, currency(15450));
    assert.equal(element('ahorro_mensual_val').innerText, currency(24800));
    const option = context.opcionesPropuesta[0];
    assert.equal(option.proem.total_100, 40250);
    assert.equal(option.proem.comision_for, 450);
    assert.equal(option.proem.total_for, 15450);
    assert.equal(option.esp.total_for, 35250);
});

test('PDF automatically captures the corrected specialized option when none is pinned', () => {
    const { context, element } = cotizador('Servicio Especializado', specialized);
    context.generarPDFMembretado({});
    assert.equal(context.opcionesPropuesta.length, 1);
    assert.equal(context.opcionesPropuesta[0].esp.total_for, 35250);
    const totalRow = element('pdf_mega_body').innerHTML.split('</tr>').find(row => row.includes('Costo Total Mensual'));
    assert.ok(totalRow.includes(currency(35250)));
});
