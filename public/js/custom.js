/**
 * PROYECTO PLATAFORMA FOR - Lógica de Interfaz y Exportación
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. INICIALIZACIÓN DE METODOLOGÍAS ---
    const metodologiasSiNo = [
        "Top Performance", "Reclutamiento Estratégico Especializado",
        "ADN de Talento", "Staffing de Talento estructural",
        "Mapeo organizacional", "Sistema de Indicadores de Gestión, dashboard y capacitación",
        "D.O Especializado", "ROI de Talento Salarial",
        "Sistema de Valuación salarial", "Arquitectura, desarrollo o Fortalecimiento soft estrategic skills",
        "Reporte Engagement and Satisfaction Pulse", "Soft Estrategic Skills",
        "Consultoría estratégica en capital humano"
    ];

    const contenedorMetodologias = document.getElementById('contenedorMetodologias');
    if (contenedorMetodologias) {
        contenedorMetodologias.innerHTML = '';
        metodologiasSiNo.forEach((m, index) => {
            contenedorMetodologias.innerHTML += `
                <div class="col-md-6 mb-3">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid #222;">
                        <label class="text-white small m-0">${m}</label>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="metodo_${index}">
                        </div>
                    </div>
                </div>`;
        });
        
        contenedorMetodologias.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox') actualizarResumen();
        });
    }

    // --- 2. LISTENERS DE INTERACTIVIDAD (CON VALIDACIÓN) ---
    
    // Intervención Parcial
	const radiosIntervencion = document.querySelectorAll('input[name="reqIntervencion"]');
	const wrapperEspecifique = document.getElementById('wrapperEspecifique');

	if (radiosIntervencion.length > 0 && wrapperEspecifique) {
	    radiosIntervencion.forEach(radio => {
	        radio.addEventListener('change', function() {
	            if (this.value === 'No' || this.value === 'Parcial') {
	                wrapperEspecifique.classList.add('is-open');
	            } else {
	                wrapperEspecifique.classList.remove('is-open');
	            }
	        });
	    });
	}

    // Análisis de Perfiles
    const switchAnalisis = document.getElementById('toggleAnalisis');
    const wrapperAnalisis = document.getElementById('wrapperAnalisis');
    if (switchAnalisis && wrapperAnalisis) {
        switchAnalisis.addEventListener('change', () => {
            if (switchAnalisis.checked) {
                wrapperAnalisis.classList.add('is-open');
                setTimeout(() => wrapperAnalisis.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 300);
            } else {
                wrapperAnalisis.classList.remove('is-open');
            }
        });
    }

    // Botón Agregar Fase 
    const btnAgregarFase = document.getElementById('btnAgregarFase');
    if (btnAgregarFase) {
        btnAgregarFase.addEventListener('click', window.agregarNuevaFase);
    }

    // Fórmula Base para Resumen
    const radiosFormula = document.querySelectorAll('input[name="formulaBase"]');
    radiosFormula.forEach(radio => radio.addEventListener('change', actualizarResumen));

    // Inicialización de vista
    if (document.getElementById('cuerpoTablaInversion')) {
        window.agregarNuevaFase(); 
    }
    actualizarResumen();

    // Inicializar los listeners para los Planes
    const radiosPlan = document.querySelectorAll('input[name="plan_integral"]');
    radiosPlan.forEach(radio => {
        radio.addEventListener('change', gestionarVisibilidadPlanes);
    });

    // Ejecución inicial por si ya hay uno seleccionado al cargar la página
    gestionarVisibilidadPlanes();

    // Listener global para la comisión: Ejecuta ambos cálculos
    document.getElementById('comision_porcentaje')?.addEventListener('input', function() {
        if(typeof window.calcularProemFinal === 'function') window.calcularProemFinal();
        if(typeof window.calcularEspecializado === 'function') window.calcularEspecializado();
    });
});

// --- 3. FUNCIONES GLOBALES (ACCESIBLES DESDE HTML) ---

window.updateRiskLevel = function(val) {
    const display = document.getElementById('risk-value-display');
    const colors = { '0': '#20FF7E', '1': '#FFD700', '2': '#FF8C00', '3': '#FF4444' };
    const color = colors[val] || '#20FF7E';

    if (display) {
        display.innerText = val;
        display.style.color = color;
        display.style.textShadow = `0 0 20px ${color}`;
    }

    let styleTag = document.getElementById('dynamic-risk-style') || document.createElement('style');
    styleTag.id = 'dynamic-risk-style';
    styleTag.innerHTML = `#riskRange::-webkit-slider-thumb { border-color: ${color} !important; box-shadow: 0 0 25px ${color} !important; }`;
    document.head.appendChild(styleTag);
};

window.toggleBenchmark = function() {
    const checkbox = document.getElementById('switchBenchmark');
    const container = document.getElementById('benchmarkFields');
    if (checkbox && container) container.style.display = checkbox.checked ? 'block' : 'none';
};

window.agregarFilaBenchmark = function() {
    const lista = document.getElementById('lista-benchmarks');
    if (!lista) return;
    
    const nuevaFila = document.createElement('div');
    nuevaFila.className = 'benchmark-row mb-4 p-3';
    nuevaFila.style.cssText = "background: rgba(0,0,0,0.3); border-radius: 10px; border: 1px solid rgba(32, 255, 126, 0.1); animation: fadeIn 0.4s ease;";
    
    nuevaFila.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge border border-neon text-neon">NUEVO PERFIL</span>
            <button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('.benchmark-row').remove()" style="font-weight: bold; font-size: 0.7rem;">
                [ ELIMINAR ]
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-12 mb-2">
                <label class="neon-text small">NOMBRE DEL PUESTO / PERFIL</label>
                <input type="text" name="bench_puesto[]" class="form-control bg-transparent text-white border-neon-dim focus-neon" placeholder="Ej. Director">
            </div>
            <div class="col-md-3">
                <label class="text-white-50 x-small d-block mb-1">25° Percentile</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-neon text-neon-dim">$</span>
                    <input type="number" step="0.01" name="bench_25[]" class="form-control bg-dark text-white border-neon-dim focus-neon" placeholder="0.00">
                </div>
            </div>
            <div class="col-md-3">
                <label class="text-white-50 x-small d-block mb-1">Mean (Promedio)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-neon text-neon-dim">$</span>
                    <input type="number" step="0.01" name="bench_mean[]" class="form-control bg-dark text-white border-neon-dim focus-neon" placeholder="0.00">
                </div>
            </div>
            <div class="col-md-3">
                <label class="text-white-50 x-small d-block mb-1">Median (Mediana)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-neon text-neon-dim">$</span>
                    <input type="number" step="0.01" name="bench_median[]" class="form-control bg-dark text-white border-neon-dim focus-neon" placeholder="0.00">
                </div>
            </div>
            <div class="col-md-3">
                <label class="text-white-50 x-small d-block mb-1">75° Percentile</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-neon text-neon-dim">$</span>
                    <input type="number" step="0.01" name="bench_75[]" class="form-control bg-dark text-white border-neon-dim focus-neon" placeholder="0.00">
                </div>
            </div>
        </div>`;
    lista.appendChild(nuevaFila);
};

window.toggleOtrosPlan = function(show) {
    const contenedor = document.getElementById('contenedor_otros_plan');
    const input = document.getElementById('otro_plan_detalle');
    if (contenedor) {
        contenedor.style.display = show ? 'block' : 'none';
        if (show && input) setTimeout(() => input.focus(), 100);
        else if (input) input.value = '';
    }
};

// --- 4. LÓGICA DE RESUMEN Y FASES ---

function actualizarResumen() {
    const contenedorResumen = document.getElementById('txtResumenDinamico');
    if (!contenedorResumen) return;
    
    let formula = document.querySelector('input[name="formulaBase"]:checked')?.value || '';
    let html = formula ? `<strong class="neon-text fs-3" style="text-transform: uppercase;">${formula}</strong>` : '';
    
    const metodos = document.querySelectorAll('#contenedorMetodologias input[type="checkbox"]:checked');
    metodos.forEach(sw => {
        const label = sw.closest('.d-flex')?.querySelector('label');
        if (label) {
            html += ` <span class="neon-text mx-2 fw-bold">+</span> <span class="text-white">${label.innerText}</span>`;
        }
    });
    
    contenedorResumen.innerHTML = html || '<span class="text-secondary small italic">Seleccione opciones arriba...</span>';
}

let contadorFases = 1;
window.agregarNuevaFase = function() {
    const cuerpo = document.getElementById('cuerpoTablaInversion');
    if (!cuerpo) return;

    const idActual = contadorFases;
    const fila = document.createElement('tr');
    fila.id = `fila_fase_${idActual}`;
    fila.innerHTML = `
        <td>
            <input type="text" class="form-control bg-transparent border-0 text-white fw-bold p-0 mb-1" value="FASE ${idActual}. ">
            <textarea class="form-control bg-transparent border-0 text-white-50 small p-0" rows="2" placeholder="Objetivo..."></textarea>
        </td>
        <td class="text-center"><input type="text" class="form-control bg-dark border-secondary text-center rounded-pill text-white mx-auto" style="width: 60px;" placeholder="0"></td>
        <td class="text-center"><input type="text" class="form-control bg-dark border-secondary text-center rounded-pill text-white mx-auto" style="width: 60px;" placeholder="0"></td>
        <td class="text-end">
            <div class="d-flex align-items-center justify-content-end">
                <span class="neon-text fw-bold me-2">$</span>
                <input type="text" class="input-table fw-bold fs-5" style="width: 150px;" placeholder="0.00">
                <span class="small text-secondary ms-2">+ IVA</span>
            </div>
        </td>
        <td class="text-center">
            ${idActual > 1 ? `<button type="button" class="btn btn-link text-danger p-0 fs-4" onclick="document.getElementById('fila_fase_${idActual}').remove()" style="text-decoration:none;">&times;</button>` : ''}
        </td>`;
    cuerpo.appendChild(fila);
    contadorFases++;
};

// --- 5. EXPORTACIÓN PDF (BOTÓN PRINCIPAL) ---
document.getElementById('btnDescargar')?.addEventListener('click', async function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    const formContainer = document.querySelector('.form-container');
    
    Swal.fire({ title: 'Generando PDF...', text: 'Optimizando para iPhone y escritorio...', allowOutsideClick: false, didOpen: () => Swal.showLoading(), background: '#111', color: '#fff' });

    formContainer.classList.add('pdf-export-mode');
    const secciones = document.querySelectorAll('.logo-container, h2, .card-req, .border-neon, .mb-4');
    let yOffset = 15;

    try {
        doc.setFillColor(0, 0, 0);
        doc.rect(0, 0, 210, 297, 'F');
        for (let sec of secciones) {
            if (sec.offsetWidth === 0 || sec.offsetHeight === 0) continue;
            const canvas = await html2canvas(sec, { scale: 2, backgroundColor: '#000000', useCORS: true, windowWidth: 1000 });
            const imgData = canvas.toDataURL('image/jpeg', 0.9);
            const imgAlto = (canvas.height * 190) / canvas.width;

            if (yOffset + imgAlto > 280) {
                doc.addPage(); doc.setFillColor(0, 0, 0); doc.rect(0, 0, 210, 297, 'F'); yOffset = 15;
            }
            doc.addImage(imgData, 'JPEG', 10, yOffset, 190, imgAlto);
            yOffset += imgAlto + 6;
        }
        doc.save(`Propuesta_FOR_${new Date().getTime()}.pdf`);
        Swal.fire({ icon: 'success', title: 'PDF Generado', background: '#111', color: '#fff' });
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo en renderizado', 'error');
    } finally {
        formContainer.classList.remove('pdf-export-mode');
    }
});

// --- 6. GESTIÓN DE PLANES (PROEM Y ESPECIALIZADOS) ---

function gestionarVisibilidadPlanes() {
    const planSeleccionado = document.querySelector('input[name="plan_integral"]:checked')?.value;
    
    const seccionProem = document.getElementById('seccion_calculo_proem');
    const seccionEspecializado = document.getElementById('seccion_calculo_especializado');
    
    if (seccionProem) seccionProem.classList.remove('is-open');
    if (seccionEspecializado) seccionEspecializado.classList.remove('is-open');

    if (planSeleccionado === "Proem" && seccionProem) {
        seccionProem.classList.add('is-open');
    } else if (planSeleccionado === "Servicio Especializado" && seccionEspecializado) {
        seccionEspecializado.classList.add('is-open');
    } else if (planSeleccionado === "Mixto") {
        if (seccionProem) seccionProem.classList.add('is-open');
        if (seccionEspecializado) seccionEspecializado.classList.add('is-open');
    }
}
window.gestionarVisibilidadPlanes = gestionarVisibilidadPlanes;

// --- CÁLCULO PROEM (FIX ESTILO EXCEL) ---
window.calcularProemFinal = function() {
    // --- 1. ESCENARIO 100% IMSS ---
    const base100 = parseFloat(document.getElementById('base_100')?.value) || 0;
    const neto100 = parseFloat(document.getElementById('neto_100')?.value) || 0;
    let carga100 = parseFloat(document.getElementById('carga_100')?.value) || 0;
    
    // Comisión que recibe el empleado (100% IMSS)
    const comision100Empleado = parseFloat(document.getElementById('comision_100')?.value) || 0;
    const afectaCarga100 = document.getElementById('comision_100_afecta_carga')?.checked || false;

    let ajusteCarga100 = 0;
    if (afectaCarga100 && comision100Empleado > 0) {
        ajusteCarga100 = comision100Empleado * 0.25; 
    }

    const total100 = base100 + carga100 + ajusteCarga100 + comision100Empleado;

    // --- 2. ESCENARIO PROPUESTA FOR (PROEM) ---
    const brutoProem = parseFloat(document.getElementById('bruto_proem')?.value) || 0;
    const netoProem = parseFloat(document.getElementById('neto_proem')?.value) || 0;
    const cargaProem = parseFloat(document.getElementById('carga_proem')?.value) || 0;

    // A) EXCEDENTE DEL TRABAJADOR
    let comisionEmpleadoProemInput = document.getElementById('comision_empleado_proem');
    let comisionEmpleadoProem = parseFloat(comisionEmpleadoProemInput?.value);
    
    // Autofill de la comisión del trabajador si la llenaron en el 100% IMSS y acá está vacía
    if (isNaN(comisionEmpleadoProem) && comision100Empleado > 0) {
        comisionEmpleadoProem = comision100Empleado;
        if (comisionEmpleadoProemInput) comisionEmpleadoProemInput.value = comision100Empleado.toFixed(2);
    } else if (isNaN(comisionEmpleadoProem)) {
        comisionEmpleadoProem = 0;
    }

    const diferenciaNetos = Math.max(0, neto100 - netoProem);
    const excedenteTotalTrabajador = diferenciaNetos + comisionEmpleadoProem;

    // B) COMISIÓN FOR (GANANCIA) -> ESTRICTAMENTE CALCULADA DEL PORCENTAJE
    const pctComisionFOR = parseFloat(document.getElementById('comision_porcentaje')?.value) || 0;
    const comisionFOR = excedenteTotalTrabajador * (pctComisionFOR / 100);

    // Pintar el resultado en el input bloqueado
    let comisionFORInput = document.getElementById('comision_for_monto');
    if (comisionFORInput) {
        comisionFORInput.value = comisionFOR > 0 ? comisionFOR.toFixed(2) : '';
    }

    // C) TOTAL PROEM = Bruto + Excedente Total + Carga Social + Comisión FOR
    const totalProem = brutoProem + excedenteTotalTrabajador + cargaProem + comisionFOR;

    // --- 3. AHORROS ---
    const ahorroMensual = total100 - totalProem;
    const ahorroAnual = ahorroMensual * 12;

    // --- 4. RENDER EN PANTALLA ---
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
    
    if(document.getElementById('total_100_display')) document.getElementById('total_100_display').innerText = fmt.format(total100);
    if(document.getElementById('total_proem_display')) document.getElementById('total_proem_display').innerText = fmt.format(totalProem);
    if(document.getElementById('excedente_val')) document.getElementById('excedente_val').innerText = fmt.format(excedenteTotalTrabajador);
    if(document.getElementById('comision_monto_val')) document.getElementById('comision_monto_val').innerText = fmt.format(comisionFOR);
    if(document.getElementById('ahorro_mensual_val')) document.getElementById('ahorro_mensual_val').innerText = fmt.format(ahorroMensual);
    if(document.getElementById('ahorro_anual_val')) document.getElementById('ahorro_anual_val').innerText = fmt.format(ahorroAnual);
};

// --- CÁLCULO SERVICIO ESPECIALIZADO (FIX ESTILO EXCEL) ---
window.calcularEspecializado = function() {
    // --- 1. ESCENARIO 100% IMSS ---
    const bruto100 = parseFloat(document.getElementById('esp_base_100')?.value) || 0; 
    const neto100 = parseFloat(document.getElementById('esp_neto_100')?.value) || 0; 
    const carga100 = parseFloat(document.getElementById('esp_carga_100')?.value) || 0; 
    
    // Costo Total 100% = Bruto + Carga Social
    const total100 = bruto100 + carga100; 

    // --- 2. ESCENARIO PROPUESTA FOR ---
    const brutoFOR = parseFloat(document.getElementById('esp_bruto_for')?.value) || 0; 
    const netoFOR = parseFloat(document.getElementById('esp_neto_for')?.value) || 0; 
    const cargaFOR = parseFloat(document.getElementById('esp_carga_for')?.value) || 0; 

    // A) Excedente
    const excedente = Math.max(0, neto100 - netoFOR);

    // B) Comisión FOR (calculado sobre el Sueldo Neto Total)
    const pctComision = parseFloat(document.getElementById('comision_porcentaje')?.value) || 0;
    const comisionFOR = neto100 * (pctComision / 100);

    // Mostrar comisión en su input bloqueado
    let comisionInput = document.getElementById('esp_comision_monto');
    if(comisionInput) comisionInput.value = comisionFOR > 0 ? comisionFOR.toFixed(2) : '';

    // C) Total Propuesta FOR = Bruto FOR + Excedente + Carga FOR + Comisión FOR
    const totalFOR = brutoFOR + excedente + cargaFOR + comisionFOR;

    // --- 3. AHORROS ---
    const ahorroMensual = total100 - totalFOR; 
    const ahorroAnual = ahorroMensual * 12;            

    // --- 4. RENDER EN PANTALLA ---
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

    if (document.getElementById('res_esp_100_total')) document.getElementById('res_esp_100_total').innerText = fmt.format(total100);
    if (document.getElementById('res_esp_for_total')) document.getElementById('res_esp_for_total').innerText = fmt.format(totalFOR);
    if (document.getElementById('esp_excedente_val')) document.getElementById('esp_excedente_val').innerText = fmt.format(excedente);
    if (document.getElementById('res_esp_diferencia')) document.getElementById('res_esp_diferencia').innerText = fmt.format(ahorroMensual);
    if (document.getElementById('esp_ahorro_anual_val')) document.getElementById('esp_ahorro_anual_val').innerText = fmt.format(ahorroAnual);

    const banner = document.getElementById('banner_esp_resultado');
    if (banner) {
        if (ahorroMensual >= 0) {
            banner.style.background = "#198754";
            banner.style.color = "#fff";
            banner.querySelector('p').innerText = "AHORRO MENSUAL";
        } else {
            banner.style.background = "#dc3545";
            banner.style.color = "#fff";
            banner.querySelector('p').innerText = "INVERSIÓN ADICIONAL";
        }
    }
};

window.obtenerFactorMensual = function() {
    const periodoSeleccionado = document.querySelector('input[name="periodo_pago"]:checked')?.value || 'Mensual';
    switch(periodoSeleccionado) {
        case 'Semanal': return 4.33;
        case 'Catorcenal': return 2.17;
        case 'Quincenal': return 2;
        case 'Mensual': default: return 1;
    }
};

window.agregarFilaPerfil = function(tipo) {
    const contenedor = document.getElementById(`contenedor_perfiles_${tipo}`);
    if (!contenedor) return;

    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 align-items-center animate-fadeIn';
    div.innerHTML = `
        <div class="col-3">
            <input type="number" name="${tipo}_cant[]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Cant.">
        </div>
        <div class="col-7">
            <input type="text" name="${tipo}_cargo[]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Cargo/Puesto">
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-sm text-danger" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    contenedor.appendChild(div);
};

// --- 8. GUARDADO EN BASE DE DATOS (AJAX) ---
function recolectarBenchmarks() {
    let benchs = [];
    const puestos = document.querySelectorAll('input[name="bench_puesto[]"]');
    const p25 = document.querySelectorAll('input[name="bench_25[]"]');
    const mean = document.querySelectorAll('input[name="bench_mean[]"]');
    const median = document.querySelectorAll('input[name="bench_median[]"]');
    const p75 = document.querySelectorAll('input[name="bench_75[]"]');
    
    for(let i = 0; i < puestos.length; i++) {
        if(puestos[i].value) { 
            benchs.push({
                puesto: puestos[i].value,
                p25: parseFloat(p25[i].value) || 0,
                mean: parseFloat(mean[i].value) || 0,
                median: parseFloat(median[i].value) || 0,
                p75: parseFloat(p75[i].value) || 0
            });
        }
    }
    return benchs;
}

function parseCurrency(str) {
    if(!str) return 0;
    return parseFloat(str.replace(/[^0-9.-]+/g,"")) || 0;
}

function recolectarPerfiles(tipo) {
    let perfiles = [];
    const cantidades = document.querySelectorAll(`input[name="${tipo}_cant[]"]`);
    const cargos = document.querySelectorAll(`input[name="${tipo}_cargo[]"]`);
    
    for(let i = 0; i < cantidades.length; i++) {
        if(cantidades[i].value.trim() !== '' || cargos[i].value.trim() !== '') {
            perfiles.push({
                cantidad: cantidades[i].value,
                cargo: cargos[i].value
            });
        }
    }
    return perfiles;
}

window.guardarPropuestaBD = async function() {
    if(typeof Swal !== 'undefined') {
        Swal.fire({ title: 'Guardando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    }

    const payload = {
        nombre_cliente: document.getElementById('nombre_cliente')?.value || '', 
        diag_360_inicial: document.getElementById('diagInicial')?.checked ? 1 : 0,
        fecha_propuesta: document.getElementById('fecha_propuesta')?.value || '',
        solicitante: document.getElementById('nombre_solicitante')?.value || '',
        cargo_solicitante: document.getElementById('cargo_solicitante')?.value || '',
        impresion_diagnostica: document.getElementById('impresion_diagnostica')?.value || '',
        req_intervencion: document.querySelector('input[name="reqIntervencion"]:checked')?.value || '',
        motivo_intervencion: document.getElementById('motivo_intervencion')?.value || '',
        nivel_riesgo: parseInt(document.getElementById('riskRange')?.value) || 0,
        benchmarks: recolectarBenchmarks(),
        plan_integral: document.querySelector('input[name="plan_integral"]:checked')?.value || '',
        otro_plan_detalle: document.getElementById('otro_plan_detalle')?.value || '',
        comision_porcentaje: parseFloat(document.getElementById('comision_porcentaje')?.value) || 0,
        periodo_pago: document.querySelector('input[name="periodo_pago"]:checked')?.value || '',
        
        // PROEM
        base_100: parseFloat(document.getElementById('base_100')?.value) || 0,
        neto_100: parseFloat(document.getElementById('neto_100')?.value) || 0,
        carga_100: parseFloat(document.getElementById('carga_100')?.value) || 0,
        comision_100: parseFloat(document.getElementById('comision_100')?.value) || 0,
        comision_100_afecta: document.getElementById('comision_100_afecta_carga')?.checked ? 1 : 0,
        comision_empleado_proem: parseFloat(document.getElementById('comision_empleado_proem')?.value) || 0,
        comision_for_monto: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('comision_monto_val')?.innerText) : 0,
        bruto_proem: parseFloat(document.getElementById('bruto_proem')?.value) || 0,
        neto_proem: parseFloat(document.getElementById('neto_proem')?.value) || 0,
        carga_proem: parseFloat(document.getElementById('carga_proem')?.value) || 0,
        total_100: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('total_100_display')?.innerText) : 0,
        excedente: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('excedente_val')?.innerText) : 0,
        total_proem: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('total_proem_display')?.innerText) : 0,
        ahorro_mensual: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('ahorro_mensual_val')?.innerText) : 0,
        perfiles_proem: recolectarPerfiles('proem'),

        // ESPECIALIZADO
        esp_base_100: parseFloat(document.getElementById('esp_base_100')?.value) || 0,
        esp_neto_100: parseFloat(document.getElementById('esp_neto_100')?.value) || 0,
        esp_carga_100: parseFloat(document.getElementById('esp_carga_100')?.value) || 0,
        esp_bruto_for: parseFloat(document.getElementById('esp_bruto_for')?.value) || 0, 
        esp_neto_for: parseFloat(document.getElementById('esp_neto_for')?.value) || 0,   
        esp_excedente: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('esp_excedente_val')?.innerText) : 0, 
        esp_carga_for: parseFloat(document.getElementById('esp_carga_for')?.value) || 0,
        esp_comision_monto: parseFloat(document.getElementById('esp_comision_monto')?.value) || 0, 
        esp_total_100: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('res_esp_100_total')?.innerText) : 0,
        esp_total_for: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('res_esp_for_total')?.innerText) : 0,
        esp_diferencia: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('res_esp_diferencia')?.innerText) : 0,
        perfiles_esp: recolectarPerfiles('esp'),
        
        // ESCENARIO ACTUAL
        actual_bruto: parseFloat(document.getElementById('actual_bruto')?.value) || 0,
        actual_nomina: parseFloat(document.getElementById('actual_nomina')?.value) || 0,
        actual_excedente: parseFloat(document.getElementById('actual_excedente')?.value) || 0,
        actual_carga: parseFloat(document.getElementById('actual_carga')?.value) || 0,
        actual_comision: parseFloat(document.getElementById('actual_comision')?.value) || 0,
        actual_total: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('total_actual_display')?.innerText) : 0,
    };

    try {
        const urlEndpoint = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + 'cotizador/guardar';
        const response = await fetch(urlEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        
        if(data.status === 'success') {
            if(typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¡Propuesta Guardada!',
                    text: 'ID: ' + data.id + ' | Generando PDF...',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.generarPDFMembretado(payload);
                });
            } else {
                alert('Guardado con éxito. ID: ' + data.id + '. Generando PDF...');
                window.generarPDFMembretado(payload);
            }
        } else {
            if(typeof Swal !== 'undefined') Swal.fire('Error', data.message, 'error');
            else alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error("Error en la petición:", error);
        if(typeof Swal !== 'undefined') Swal.fire('Error', 'Fallo la conexión con el servidor', 'error');
    }
};

window.actualizarCotizacionExistente = async function(generarPdf = false) {
    if(typeof Swal !== 'undefined') Swal.fire({ title: 'Actualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    const propuestaId = document.getElementById('propuesta_id').value;
    const payload = {
        nombre_cliente: document.getElementById('nombre_cliente')?.value || '', 
        diag_360_inicial: document.getElementById('diagInicial')?.checked ? 1 : 0,
        fecha_propuesta: document.getElementById('fecha_propuesta')?.value || '',
        solicitante: document.getElementById('nombre_solicitante')?.value || '',
        cargo_solicitante: document.getElementById('cargo_solicitante')?.value || '',
        impresion_diagnostica: document.getElementById('impresion_diagnostica')?.value || '',
        req_intervencion: document.querySelector('input[name="reqIntervencion"]:checked')?.value || '',
        motivo_intervencion: document.getElementById('motivo_intervencion')?.value || '',
        nivel_riesgo: parseInt(document.getElementById('riskRange')?.value) || 0,
        benchmarks: typeof recolectarBenchmarks === 'function' ? recolectarBenchmarks() : [],
        plan_integral: document.querySelector('input[name="plan_integral"]:checked')?.value || '',
        otro_plan_detalle: document.getElementById('otro_plan_detalle')?.value || '',
        comision_porcentaje: parseFloat(document.getElementById('comision_porcentaje')?.value) || 0,
        periodo_pago: document.querySelector('input[name="periodo_pago"]:checked')?.value || '',
        
        // PROEM
        base_100: parseFloat(document.getElementById('base_100')?.value) || 0,
        neto_100: parseFloat(document.getElementById('neto_100')?.value) || 0,
        carga_100: parseFloat(document.getElementById('carga_100')?.value) || 0,
        comision_100: parseFloat(document.getElementById('comision_100')?.value) || 0,
        comision_100_afecta: document.getElementById('comision_100_afecta_carga')?.checked ? 1 : 0,
        comision_empleado_proem: parseFloat(document.getElementById('comision_empleado_proem')?.value) || 0,
        comision_for_monto: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('comision_monto_val')?.innerText) : 0,
        bruto_proem: parseFloat(document.getElementById('bruto_proem')?.value) || 0,
        neto_proem: parseFloat(document.getElementById('neto_proem')?.value) || 0,
        carga_proem: parseFloat(document.getElementById('carga_proem')?.value) || 0,
        total_100: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('total_100_display')?.innerText) : 0,
        excedente: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('excedente_val')?.innerText) : 0,
        total_proem: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('total_proem_display')?.innerText) : 0,
        ahorro_mensual: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('ahorro_mensual_val')?.innerText) : 0,
        perfiles_proem: typeof recolectarPerfiles === 'function' ? recolectarPerfiles('proem') : [],

        // ESPECIALIZADO
        esp_base_100: parseFloat(document.getElementById('esp_base_100')?.value) || 0,
        esp_neto_100: parseFloat(document.getElementById('esp_neto_100')?.value) || 0,
        esp_carga_100: parseFloat(document.getElementById('esp_carga_100')?.value) || 0,
        esp_bruto_for: parseFloat(document.getElementById('esp_bruto_for')?.value) || 0, 
        esp_neto_for: parseFloat(document.getElementById('esp_neto_for')?.value) || 0,   
        esp_excedente: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('esp_excedente_val')?.innerText) : 0, 
        esp_carga_for: parseFloat(document.getElementById('esp_carga_for')?.value) || 0,
        esp_comision_monto: parseFloat(document.getElementById('esp_comision_monto')?.value) || 0, 
        esp_total_100: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('res_esp_100_total')?.innerText) : 0,
        esp_total_for: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('res_esp_for_total')?.innerText) : 0,
        esp_diferencia: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('res_esp_diferencia')?.innerText) : 0,
        perfiles_esp: typeof recolectarPerfiles === 'function' ? recolectarPerfiles('esp') : [],
        
        // ESCENARIO ACTUAL
        actual_bruto: parseFloat(document.getElementById('actual_bruto')?.value) || 0,
        actual_nomina: parseFloat(document.getElementById('actual_nomina')?.value) || 0,
        actual_excedente: parseFloat(document.getElementById('actual_excedente')?.value) || 0,
        actual_carga: parseFloat(document.getElementById('actual_carga')?.value) || 0,
        actual_comision: parseFloat(document.getElementById('actual_comision')?.value) || 0,
        actual_total: typeof parseCurrency === 'function' ? parseCurrency(document.getElementById('total_actual_display')?.innerText) : 0,
    };

    try {
        const urlEndpoint = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + 'cotizador/actualizar/' + propuestaId;
        const response = await fetch(urlEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        
        if(data.status === 'success') {
            if (generarPdf) {
                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¡Actualizado!',
                        text: 'Generando PDF...',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => window.generarPDFMembretado(payload));
                } else {
                    window.generarPDFMembretado(payload);
                }
            } else {
                if(typeof Swal !== 'undefined') {
                    Swal.fire('¡Actualizado!', 'La cotización se guardó correctamente.', 'success').then(() => {
                        window.location.href = BASE_URL + 'cotizador';
                    });
                } else {
                    alert('Actualizado con éxito.');
                    window.location.href = BASE_URL + 'cotizador';
                }
            }
        } else {
            if(typeof Swal !== 'undefined') Swal.fire('Error', data.message, 'error');
            else alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error("Error:", error);
        if(typeof Swal !== 'undefined') Swal.fire('Error', 'Fallo la conexión con el servidor', 'error');
    }
};

window.calcularBaselineActual = function() {
    const bruto = parseFloat(document.getElementById('actual_bruto')?.value) || 0;
    const nomina = parseFloat(document.getElementById('actual_nomina')?.value) || 0; 
    const excedente = parseFloat(document.getElementById('actual_excedente')?.value) || 0;
    const carga = parseFloat(document.getElementById('actual_carga')?.value) || 0;
    const comision = parseFloat(document.getElementById('actual_comision')?.value) || 0;

    const total = nomina + excedente + carga + comision;
    
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
    if(document.getElementById('total_actual_display')) {
        document.getElementById('total_actual_display').innerText = fmt.format(total);
    }

    if(typeof window.calcularProemFinal === 'function') window.calcularProemFinal();
    if(typeof window.calcularEspecializado === 'function') window.calcularEspecializado();
};

window.opcionesPropuesta = [];

window.fijarOpcionActual = function() {
    if (!window.opcionesPropuesta) window.opcionesPropuesta = [];
    
    const tipoPlan = document.querySelector('input[name="plan_integral"]:checked')?.value || 'Proem';
    const idOpcion = window.opcionesPropuesta.length + 1;
    const pctComision = parseFloat(document.getElementById('comision_porcentaje')?.value) || 0;

    let opcion = {
        id_opcion: idOpcion,
        tipo_plan: tipoPlan,
        pct_comision: pctComision
    };

    if (tipoPlan === 'Proem' || tipoPlan === 'Mixto') {
        const base100 = parseFloat(document.getElementById('base_100')?.value) || 0;
        const neto100 = parseFloat(document.getElementById('neto_100')?.value) || 0;
        const carga100 = parseFloat(document.getElementById('carga_100')?.value) || 0;
        
        const comision100Emp = parseFloat(document.getElementById('comision_100')?.value) || 0;
        const afectaCarga100 = document.getElementById('comision_100_afecta_carga')?.checked || false;
        const ajusteCarga100 = (afectaCarga100 && comision100Emp > 0) ? (comision100Emp * 0.25) : 0;
        const totalCarga100Efectiva = carga100 + ajusteCarga100;

        const brutoProem = parseFloat(document.getElementById('bruto_proem')?.value) || 0;
        const netoProem = parseFloat(document.getElementById('neto_proem')?.value) || 0;
        const cargaProem = parseFloat(document.getElementById('carga_proem')?.value) || 0;

        let comEmpProem = parseFloat(document.getElementById('comision_empleado_proem')?.value);
        if (isNaN(comEmpProem) && comision100Emp > 0) comEmpProem = comision100Emp;
        else if (isNaN(comEmpProem)) comEmpProem = 0;

        const diferenciaNetos = Math.max(0, neto100 - netoProem);
        const excedenteTotal = diferenciaNetos + comEmpProem;
        const comisionFOR = excedenteTotal * (pctComision / 100);

        opcion.proem = {
            base_100: base100,
            neto_100: neto100,
            carga_100: totalCarga100Efectiva,
            comision_100_emp: comision100Emp,
            
            bruto_for: brutoProem,
            neto_for: netoProem,
            carga_for: cargaProem,
            comision_emp_proem: comEmpProem,
            excedente: excedenteTotal,
            comision_for: comisionFOR,

            total_100: base100 + totalCarga100Efectiva + comision100Emp,
            total_for: brutoProem + excedenteTotal + cargaProem + comisionFOR
        };
    }

    if (tipoPlan === 'Servicio Especializado' || tipoPlan === 'Mixto') {
        const neto100 = parseFloat(document.getElementById('esp_neto_100')?.value) || 0;
        const netoFOR = parseFloat(document.getElementById('esp_neto_for')?.value) || 0;

        opcion.esp = {
            base_100: parseFloat(document.getElementById('esp_base_100')?.value) || 0,
            neto_100: neto100,
            carga_100: parseFloat(document.getElementById('esp_carga_100')?.value) || 0,
            
            sueldos_for: parseFloat(document.getElementById('esp_bruto_for')?.value) || 0,
            neto_for: netoFOR,
            carga_for: parseFloat(document.getElementById('esp_carga_for')?.value) || 0,
            
            excedente: Math.max(0, neto100 - netoFOR),
            comision_monto: neto100 * (pctComision / 100)
        };
        opcion.esp.total_100 = opcion.esp.base_100 + opcion.esp.carga_100;
        opcion.esp.total_for = opcion.esp.sueldos_for + opcion.esp.excedente + opcion.esp.carga_for + opcion.esp.comision_monto;
    }

    window.opcionesPropuesta.push(opcion);

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success', title: '¡Opción Fijada!', timer: 1500, showConfirmButton: false, background: '#111', color: '#20FF7E'
        });
    }

    renderizarOpcionesFijadas();
};

function renderizarOpcionesFijadas() {
    const contenedor = document.getElementById('opciones_fijadas_container');
    const lista = document.getElementById('lista_opciones_fijadas');
    
    if (window.opcionesPropuesta.length > 0) {
        contenedor.style.display = 'block';
        lista.innerHTML = '';
        
        window.opcionesPropuesta.forEach(opc => {
            lista.innerHTML += `
                <span class="badge bg-warning text-dark fs-6 px-3 py-2" style="border: 1px solid #cca100;">
                    Opción ${opc.id_opcion}: ${opc.tipo_plan}
                </span>`;
        });
    } else {
        contenedor.style.display = 'none';
        lista.innerHTML = '';
    }
}

window.limpiarOpciones = function() {
    window.opcionesPropuesta = [];
    document.getElementById('lista_opciones_fijadas').innerHTML = '';
    document.getElementById('opciones_fijadas_container').style.display = 'none';
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info', title: 'Opciones reiniciadas', text: 'La mochila está vacía.', background: 'rgba(0,0,0,0.9)', color: '#FFD700', showConfirmButton: false, timer: 2000
        });
    }
};

window.generarPDFMembretado = function(payload) {
    const fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

    const fechaActual = new Date().toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' });
    if(document.getElementById('pdf_fecha_impresion')) document.getElementById('pdf_fecha_impresion').innerText = "Fecha: " + fechaActual;
    if(document.getElementById('pdf_cliente')) document.getElementById('pdf_cliente').innerText = payload.nombre_cliente || 'N/A';
    if(document.getElementById('pdf_solicitante')) document.getElementById('pdf_solicitante').innerText = payload.solicitante || 'N/A';
    if(document.getElementById('pdf_nivel_riesgo')) document.getElementById('pdf_nivel_riesgo').innerText = payload.nivel_riesgo || 'N/A';
    if(document.getElementById('pdf_impresion')) document.getElementById('pdf_impresion').innerText = payload.impresion_diagnostica || 'Sin observaciones.';
    if(document.getElementById('pdf_periodo_pago')) document.getElementById('pdf_periodo_pago').innerText = payload.periodo_pago || 'No especificado';

    let textoPerfiles = [];
    if(payload.perfiles_proem && payload.perfiles_proem.length > 0) textoPerfiles.push("PROEM: " + payload.perfiles_proem.map(p => `${p.cantidad} ${p.cargo}`).join(', '));
    if(payload.perfiles_esp && payload.perfiles_esp.length > 0) textoPerfiles.push("ESP: " + payload.perfiles_esp.map(p => `${p.cantidad} ${p.cargo}`).join(', '));
    if(document.getElementById('pdf_resumen_plantillas')) {
        document.getElementById('pdf_resumen_plantillas').innerHTML = `<strong>Plantilla:</strong> ${textoPerfiles.length > 0 ? textoPerfiles.join(' | ') : 'No especificada'}`;
    }

    const actBruto = parseFloat(document.getElementById('actual_bruto')?.value) || 0;
    const actNeto = parseFloat(document.getElementById('actual_nomina')?.value) || 0;
    const actExcedente = parseFloat(document.getElementById('actual_excedente')?.value) || 0;
    const actCarga = parseFloat(document.getElementById('actual_carga')?.value) || 0;
    const actComision = parseFloat(document.getElementById('actual_comision')?.value) || 0;
    const totalActual = actNeto + actExcedente + actCarga + actComision;

    // --- CÁLCULOS 100% IMSS PARA EL PDF ---
    const imssProemBase = parseFloat(document.getElementById('base_100')?.value) || 0;
    const imssProemNeto = parseFloat(document.getElementById('neto_100')?.value) || 0;
    const imssProemCarga = parseFloat(document.getElementById('carga_100')?.value) || 0;
    const comision100Emp = parseFloat(document.getElementById('comision_100')?.value) || 0;
    
    const afectaCarga100 = document.getElementById('comision_100_afecta_carga')?.checked || false;
    const ajusteCarga100 = (afectaCarga100 && comision100Emp > 0) ? (comision100Emp * 0.25) : 0;
    const totalCarga100Efectiva = imssProemCarga + ajusteCarga100;
    
    const imssEspBase = parseFloat(document.getElementById('esp_base_100')?.value) || 0;
    const imssEspNeto = parseFloat(document.getElementById('esp_neto_100')?.value) || 0;
    const imssEspCarga = parseFloat(document.getElementById('esp_carga_100')?.value) || 0;

    const base100 = imssProemBase + imssEspBase;
    const neto100 = imssProemNeto + imssEspNeto;
    const carga100 = totalCarga100Efectiva + imssEspCarga;
    const total100 = base100 + carga100 + comision100Emp;

    if (!window.opcionesPropuesta || window.opcionesPropuesta.length === 0) {
        if (total100 > 0 && typeof window.fijarOpcionActual === 'function') {
            window.fijarOpcionActual(); 
        }
    }

    const head = document.getElementById('pdf_mega_head');
    const body = document.getElementById('pdf_mega_body');
    const mostrarActual = totalActual > 0;
    const mostrarIMSS = total100 > 0;

    const opcionesValidas = (window.opcionesPropuesta || []).filter(opc => {
        let oTotal = 0;
        if(opc.proem) oTotal += opc.proem.total_for;
        if(opc.esp) oTotal += opc.esp.total_for;
        return oTotal > 0;
    });

    if (!mostrarActual && !mostrarIMSS && opcionesValidas.length === 0) {
        if(head) head.innerHTML = "";
        if(body) body.innerHTML = "";
    } else {
        let thHtml = `<tr style="font-size: 11.5px; height: 28px;">
            <th style="background-color: #333; color: #fff; width: 22%; text-align: left; padding: 4px 6px;">Concepto</th>`;
        
        if (mostrarActual) thHtml += `<th style="background-color: #f1f1f1; color: #333; padding: 4px 6px; width: 15%;">Actual</th>`;
        if (mostrarIMSS) thHtml += `<th style="background-color: #f1f1f1; color: #333; padding: 4px 6px; width: 15%;">100% IMSS</th>`;
        
        opcionesValidas.forEach(opc => {
            thHtml += `<th style="background-color: #20FF7E; color: #000; padding: 4px 6px; font-weight: bold;">Opción FOR ${opc.id_opcion} <span style="font-size: 9.5px; font-weight: normal;">(${opc.tipo_plan === 'Servicio Especializado' ? 'ESP' : opc.tipo_plan})</span></th>`;
        });
        thHtml += `</tr>`;
        if(head) head.innerHTML = thHtml;

        const cellStyle = "padding: 4px 6px; border-bottom: 1px solid #eee; font-size: 11.5px; white-space: nowrap;";
        
        let rowBruto = `<tr style="height: 24px;"><td style="text-align: left; ${cellStyle}">Salarios Brutos</td>${mostrarActual ? `<td style="${cellStyle}">${fmt.format(actBruto)}</td>` : ''}${mostrarIMSS ? `<td style="${cellStyle}">${fmt.format(base100)}</td>` : ''}`;
        let rowNeto = `<tr style="height: 24px;"><td style="text-align: left; ${cellStyle} color: #666; font-style: italic;">Sueldos Netos (Info)</td>${mostrarActual ? `<td style="${cellStyle} color: #666; font-style: italic;">${fmt.format(actNeto)}</td>` : ''}${mostrarIMSS ? `<td style="${cellStyle} color: #666; font-style: italic;">${fmt.format(neto100)}</td>` : ''}`;
        let rowComisionEmp = `<tr style="height: 24px;"><td style="text-align: left; ${cellStyle}">Comisión Empleado</td>${mostrarActual ? `<td style="${cellStyle}">-</td>` : ''}${mostrarIMSS ? `<td style="${cellStyle}">${comision100Emp > 0 ? fmt.format(comision100Emp) : '-'}</td>` : ''}`;
        let rowExcedenteSueldo = `<tr style="height: 24px;"><td style="text-align: left; ${cellStyle}">Excedente Sueldo Neto</td>${mostrarActual ? `<td style="${cellStyle}">${fmt.format(actExcedente)}</td>` : ''}${mostrarIMSS ? `<td style="${cellStyle} color: #ccc;">-</td>` : ''}`;
        let rowCarga = `<tr style="height: 24px;"><td style="text-align: left; ${cellStyle}">Costo Social de Impuesto</td>${mostrarActual ? `<td style="${cellStyle}">${fmt.format(actCarga)}</td>` : ''}${mostrarIMSS ? `<td style="${cellStyle}">${fmt.format(carga100)}</td>` : ''}`;
        let rowComisionFOR = `<tr style="height: 24px;"><td style="text-align: left; padding: 4px 6px; border-bottom: 1.5px solid #333; font-weight: bold; font-size: 11.5px; white-space: nowrap;">Comisión FOR (Ganancia)</td>${mostrarActual ? `<td style="padding: 4px 6px; border-bottom: 1.5px solid #333; font-size: 11.5px;">-</td>` : ''}${mostrarIMSS ? `<td style="padding: 4px 6px; border-bottom: 1.5px solid #333; color: #ccc; font-size: 11.5px;">-</td>` : ''}`;
        let rowTotal = `<tr style="background-color: #f9f9f9; height: 26px;"><td style="text-align: left; padding: 5px 6px; font-weight: bold; border-top: 1px solid #333; font-size: 12.5px; white-space: nowrap;">Costo Total Mensual</td>${mostrarActual ? `<td style="padding: 5px 6px; font-weight: bold; color: #000; border-top: 1px solid #333; font-size: 12.5px;">${fmt.format(totalActual)}</td>` : ''}${mostrarIMSS ? `<td style="padding: 5px 6px; font-weight: bold; color: #000; border-top: 1px solid #333; font-size: 12.5px;">${fmt.format(total100)}</td>` : ''}`;
        
        let rowAhorroMensual = `<tr style="height: 24px;"><td style="text-align: left; padding: 4px 6px; font-weight: bold; color: #198754; font-size: 11.5px; white-space: nowrap;">Ahorro Mensual</td>${mostrarActual ? `<td style="padding: 4px 6px; color: #ccc;">-</td>` : ''}${mostrarIMSS ? `<td style="padding: 4px 6px; color: #ccc;">-</td>` : ''}`;
        let rowAhorroAnual = `<tr style="height: 24px;"><td style="text-align: left; padding: 4px 6px; font-weight: bold; color: #198754; border-bottom: 2px solid #20FF7E; font-size: 11.5px; white-space: nowrap;">Ahorro Anual</td>${mostrarActual ? `<td style="padding: 4px 6px; color: #ccc; border-bottom: 2px solid #20FF7E;">-</td>` : ''}${mostrarIMSS ? `<td style="padding: 4px 6px; color: #ccc; border-bottom: 2px solid #20FF7E;">-</td>` : ''}`;

        opcionesValidas.forEach(opc => {
            let oBruto = 0, oNeto = 0, oComisionEmp = 0, oExcedenteNeto = 0, oCarga = 0, oComisionFOR = 0, oTotal = 0;

            if(opc.proem) {
                oBruto = opc.proem.bruto_for;
                oNeto = opc.proem.neto_for; 
                oComisionEmp = opc.proem.comision_emp_proem;
                oExcedenteNeto = Math.max(0, opc.proem.neto_100 - opc.proem.neto_for); 
                oCarga = opc.proem.carga_for; 
                oComisionFOR = opc.proem.comision_for; 
                oTotal = opc.proem.total_for;
            }
            if(opc.esp) {
                oBruto = opc.esp.sueldos_for; 
                oNeto = opc.esp.neto_for; 
                oComisionEmp = 0; 
                oExcedenteNeto = opc.esp.excedente; 
                oCarga = opc.esp.carga_for; 
                oComisionFOR = opc.esp.comision_monto; 
                oTotal = opc.esp.total_for;
            }

            let ahorroM = total100 - oTotal;
            let ahorroA = ahorroM * 12;

            rowBruto += `<td style="${cellStyle}">${oBruto > 0 ? fmt.format(oBruto) : '-'}</td>`;
            rowNeto += `<td style="${cellStyle} color: #666; font-style: italic;">${fmt.format(oNeto)}</td>`;
            rowComisionEmp += `<td style="${cellStyle}">${oComisionEmp > 0 ? fmt.format(oComisionEmp) : '-'}</td>`;
            rowExcedenteSueldo += `<td style="${cellStyle}">${oExcedenteNeto > 0 ? fmt.format(oExcedenteNeto) : '-'}</td>`;
            rowCarga += `<td style="${cellStyle}">${fmt.format(oCarga)}</td>`;
            rowComisionFOR += `<td style="padding: 4px 6px; border-bottom: 1.5px solid #333; background-color: #f9fff9; font-size: 11.5px;">${fmt.format(oComisionFOR)} <span style="font-size: 9.5px; color: #666;">(${opc.pct_comision}%)</span></td>`;
            rowTotal += `<td style="padding: 5px 6px; font-weight: bold; color: #198754; border: 1.5px solid #333; background-color: #e6ffe6; font-size: 12.5px;">${fmt.format(oTotal)}</td>`;
            
            rowAhorroMensual += `<td style="padding: 4px 6px; font-weight: bold; font-size: 11.5px; color: ${ahorroM >= 0 ? '#198754' : '#dc3545'};">${ahorroM >= 0 ? '+' : ''}${fmt.format(ahorroM)}</td>`;
            rowAhorroAnual += `<td style="padding: 4px 6px; font-weight: bold; font-size: 11.5px; color: ${ahorroA >= 0 ? '#198754' : '#dc3545'}; border-bottom: 2px solid #20FF7E;">${ahorroA >= 0 ? '+' : ''}${fmt.format(ahorroA)}</td>`;
        });

        rowBruto += `</tr>`; rowNeto += `</tr>`; rowComisionEmp += `</tr>`; rowExcedenteSueldo += `</tr>`; rowCarga += `</tr>`; 
        rowComisionFOR += `</tr>`; rowTotal += `</tr>`; rowAhorroMensual += `</tr>`; rowAhorroAnual += `</tr>`;

        if(body) body.innerHTML = rowBruto + rowNeto + rowComisionEmp + rowExcedenteSueldo + rowCarga + rowComisionFOR + rowTotal + rowAhorroMensual + rowAhorroAnual;
    }

    // --- RENDERIZADO DEL BENCHMARK ---
    let benchmarkContainer = document.getElementById('pdf_benchmark_container');
    if (!benchmarkContainer) {
        benchmarkContainer = document.createElement('div');
        benchmarkContainer.id = 'pdf_benchmark_container';
        const tablaFinanciera = document.getElementById('pdf_mega_head')?.parentElement;
        if(tablaFinanciera) tablaFinanciera.insertAdjacentElement('afterend', benchmarkContainer);
    }

    if (payload.benchmarks && payload.benchmarks.length > 0) {
        let benchHtml = `
            <div style="margin-top: 15px; font-family: Arial, sans-serif;">
                <h4 style="margin: 0 0 5px 0; font-size: 13px; font-weight: bold; color: #333; border-bottom: 2px solid #20FF7E; padding-bottom: 2px; display: inline-block;">Market Benchmark</h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 5px;">
                    <thead>
                        <tr style="background-color: #333; color: #fff; height: 26px;">
                            <th style="padding: 4px 6px; text-align: left; width: 30%;">Perfil / Puesto</th>
                            <th style="padding: 4px 6px; text-align: center;">25° Percentil</th>
                            <th style="padding: 4px 6px; text-align: center;">Promedio</th>
                            <th style="padding: 4px 6px; text-align: center;">Mediana</th>
                            <th style="padding: 4px 6px; text-align: center;">75° Percentil</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        payload.benchmarks.forEach((bench, index) => {
            const bg = index % 2 === 0 ? '#f9f9f9' : '#fff';
            benchHtml += `
                <tr style="background-color: ${bg}; border-bottom: 1px solid #eee; height: 24px;">
                    <td style="padding: 4px 6px; font-weight: bold; color: #333;">${bench.puesto.toUpperCase()}</td>
                    <td style="padding: 4px 6px; text-align: center;">${fmt.format(bench.p25)}</td>
                    <td style="padding: 4px 6px; text-align: center;">${fmt.format(bench.mean)}</td>
                    <td style="padding: 4px 6px; text-align: center;">${fmt.format(bench.median)}</td>
                    <td style="padding: 4px 6px; text-align: center;">${fmt.format(bench.p75)}</td>
                </tr>
            `;
        });
        
        benchHtml += `</tbody></table></div>`;
        benchmarkContainer.innerHTML = benchHtml;
        benchmarkContainer.style.display = 'block';
    } else {
        benchmarkContainer.innerHTML = '';
        benchmarkContainer.style.display = 'none';
    }

    // --- PROCESO FINAL html2pdf ---
    const elementoMolde = document.getElementById('contenido_pdf');
    const contenedorPadre = document.getElementById('plantilla_pdf_container');
    
    if(contenedorPadre) {
        contenedorPadre.style.display = 'block';
        contenedorPadre.style.position = 'fixed';
        contenedorPadre.style.top = '200vh';
        contenedorPadre.style.width = '1050px';
    }

    const tablaEstilo = document.querySelector('#contenido_pdf table');
    if(tablaEstilo) {
        tablaEstilo.style.transform = "scale(0.88)";
        tablaEstilo.style.transformOrigin = "top left";
        tablaEstilo.style.marginBottom = "-45px";
    }

    const opcionesPDF = {
        margin: [0.25, 0.3, 0.1, 0.3], 
        filename: `Propuesta_FOR_${(payload.nombre_cliente || 'Cliente').replace(/\s+/g, '_')}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' },
        pagebreak: { mode: ['avoid-all'] }
    };

    setTimeout(() => {
        html2pdf().set(opcionesPDF).from(elementoMolde).save().then(() => {
            if(contenedorPadre) contenedorPadre.style.display = 'none';
            if(tablaEstilo) {
                tablaEstilo.style.transform = "";
                tablaEstilo.style.marginBottom = "";
            }
        });
    }, 600);
};
