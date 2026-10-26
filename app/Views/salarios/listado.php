<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Tablas salariales</h2>
        <p class="text-muted mb-0">Referencias de mercado por puesto, especialidad y contexto.</p>
    </div>
    <?php if ($disponible && $total > 0): ?>
        <a href="<?= esc(salarios_url($filtros, [], 'salarios/exportar'), 'attr') ?>" class="btn btn-outline-success shadow-sm">
            <i class="bi bi-download me-1" aria-hidden="true"></i> Exportar resultados CSV
        </a>
    <?php endif; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="small text-muted">Referencias encontradas</div>
        <div class="fs-3 fw-bold text-primary"><?= number_format($total) ?></div>
    </div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="small text-muted">Edición seleccionada</div>
        <div class="fw-bold mt-2"><?= esc($guia['titulo'] ?? 'Sin edición seleccionada') ?></div>
    </div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="small text-muted">Fuente</div>
        <div class="fw-bold mt-2"><?= esc($guia['editorial'] ?? '—') ?></div>
        <div class="small text-muted">Cada referencia conserva su página de origen.</div>
    </div></div></div>
</div>

<?php if ($disponible): ?>
    <div class="alert alert-info small mb-4">
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
        Consulta documental: importes literales del PDF, con unidades pendientes de validación editorial.
        La metodología general indica sueldo bruto mensual; existen filas diarias y anuales.
        Revisa la moneda, el periodo y las observaciones antes de comparar. No se aplican conversiones.
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4 bg-light"><div class="card-body">
    <form action="<?= esc(site_url('salarios'), 'attr') ?>" method="get" id="salariosFiltros">
        <div class="row g-3 align-items-start">
            <div class="col-lg-4 col-md-6">
                <label for="salBuscar" class="form-label small fw-bold">Buscar puesto</label>
                <div class="position-relative" id="salBuscarContenedor">
                    <input id="salBuscar" name="buscar" class="form-control form-control-sm" maxlength="120"
                           value="<?= esc($filtros['buscar'], 'attr') ?>" placeholder="Ej. Gerente de Finanzas o Desarrollador"
                           role="combobox" aria-autocomplete="list" aria-expanded="false"
                           aria-controls="salPuestosSugerencias" aria-describedby="salBuscarAyuda salBuscarEstado"
                           autocomplete="off" spellcheck="false"
                           data-sugerencias-url="<?= esc(site_url('salarios/puestos'), 'attr') ?>">
                    <ul id="salPuestosSugerencias" role="listbox" aria-label="Puestos sugeridos" hidden
                        class="list-group position-absolute top-100 start-0 w-100 shadow bg-white"
                        style="z-index: 1050; max-height: 18rem; overflow-y: auto;"></ul>
                </div>
                <div id="salBuscarAyuda" class="small text-muted mt-1">Escribe al menos 2 caracteres para ver sugerencias.</div>
                <div id="salBuscarEstado" class="small text-muted" role="status" aria-live="polite" style="min-height: 1.25rem;"></div>
            </div>
            <div class="col-lg-4 col-md-6">
                <label for="salGuia" class="form-label small fw-bold">Edición</label>
                <select id="salGuia" name="guia" class="form-select form-select-sm">
                    <?php if (!$catalogos['guias']): ?><option value="">Sin ediciones</option><?php endif; ?>
                    <?php foreach ($catalogos['guias'] as $item): ?>
                        <option value="<?= (int) $item['id'] ?>" <?= $filtros['guia'] === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-4 col-md-6">
                <label for="salArea" class="form-label small fw-bold">Área</label>
                <select id="salArea" name="area" class="form-select form-select-sm">
                    <option value="">Todas las áreas</option>
                    <?php foreach ($catalogos['areas'] as $value): ?>
                        <option value="<?= esc($value, 'attr') ?>" <?= $filtros['area'] === $value ? 'selected' : '' ?>><?= esc($value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php $basicFilters = [
                'zona' => ['Zona', ['Centro/Sur' => 'Centro/Sur', 'Occidente/Bajío' => 'Occidente/Bajío', 'Norte' => 'Norte', 'Noroeste' => 'Noroeste', 'Nacional' => 'Nacional']],
                'modalidad' => ['Modalidad', ['hibrido' => 'Híbrido', 'remoto' => 'Remoto', 'no_informada' => 'No informada']],
                'moneda' => ['Moneda', ['MXN' => 'MXN', 'USD' => 'USD', 'pendiente' => 'Por confirmar']],
                'periodicidad' => ['Periodo del sueldo', ['mensual' => 'Mensual', 'diario' => 'Diario', 'anual' => 'Anual']],
            ]; ?>
            <?php foreach ($basicFilters as $key => [$label, $options]): ?>
                <div class="col-lg-3 col-md-6">
                    <label for="sal-<?= esc($key, 'attr') ?>" class="form-label small fw-bold"><?= esc($label) ?></label>
                    <select id="sal-<?= esc($key, 'attr') ?>" name="<?= esc($key, 'attr') ?>" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach ($options as $value => $text): ?>
                            <option value="<?= esc($value, 'attr') ?>" <?= $filtros[$key] === $value ? 'selected' : '' ?>><?= esc($text) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>
        </div>
        <details class="mt-3" <?= $filtros['subarea'] || $filtros['facturacion'] || $filtros['estado'] || $filtros['indicador'] || $filtros['orden'] ? 'open' : '' ?>>
            <summary class="small fw-semibold text-primary">Especialidad y más filtros</summary>
            <div class="row g-3 mt-0">
                <div class="col-lg-4 col-md-6">
                    <label for="salSubarea" class="form-label small fw-bold">Especialidad</label>
                    <select id="salSubarea" name="subarea" class="form-select form-select-sm">
                        <option value="">Todas las especialidades</option>
                        <?php foreach ($catalogos['subareas'] as $value): ?>
                            <option value="<?= esc($value, 'attr') ?>" <?= $filtros['subarea'] === $value ? 'selected' : '' ?>><?= esc($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label for="salFacturacion" class="form-label small fw-bold">Facturación anual de empresa (C-Suite)</label>
                    <select id="salFacturacion" name="facturacion" class="form-select form-select-sm">
                        <option value="">Todos los segmentos</option>
                        <?php foreach ($catalogos['facturaciones'] as $value): ?>
                            <option value="<?= esc($value, 'attr') ?>" <?= $filtros['facturacion'] === $value ? 'selected' : '' ?>><?= esc($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php $extraFilters = [
                    'estado' => ['Revisión', ['extraido' => 'Extraído, sin incidencias detectadas', 'con_observaciones' => 'Con observaciones']],
                    'indicador' => ['Indicadores de la guía', ['nueva' => 'Posición nueva (N)', 'demanda' => 'Mayor demanda (D)']],
                    'orden' => ['Ordenar por', ['puesto' => 'Puesto', 'area' => 'Área', 'pagina' => 'Página del PDF']],
                ]; ?>
                <?php foreach ($extraFilters as $key => [$label, $options]): ?>
                    <div class="col-lg-4 col-md-6">
                        <label for="sal-<?= esc($key, 'attr') ?>" class="form-label small fw-bold"><?= esc($label) ?></label>
                        <select id="sal-<?= esc($key, 'attr') ?>" name="<?= esc($key, 'attr') ?>" class="form-select form-select-sm">
                            <option value="">Todos / predeterminado</option>
                            <?php foreach ($options as $value => $text): ?>
                                <option value="<?= esc($value, 'attr') ?>" <?= $filtros[$key] === $value ? 'selected' : '' ?>><?= esc($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
        <div class="d-flex gap-2 justify-content-end mt-3">
            <a href="<?= esc(site_url('salarios'), 'attr') ?>" class="btn btn-outline-secondary btn-sm">Limpiar filtros</a>
            <button class="btn btn-primary btn-sm px-4" type="submit"><i class="bi bi-search me-1" aria-hidden="true"></i>Consultar</button>
        </div>
    </form>
</div></div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <caption class="px-3 small">Valores publicados por la guía. Med es el valor medio publicado; un guion indica dato no informado.</caption>
            <thead class="table-light"><tr>
                <th scope="col" class="ps-3">Puesto / especialidad</th>
                <th scope="col">Contexto</th><th scope="col">Moneda / periodo</th>
                <th scope="col" class="text-end">Mínimo</th><th scope="col" class="text-end">Med</th>
                <th scope="col" class="text-end">Máximo</th><th scope="col">Variable</th>
                <th scope="col">Fuente</th><th scope="col" class="pe-3">Detalle</th>
            </tr></thead>
            <tbody>
                <?php foreach ($referencias as $row): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold"><?= esc($row['puesto']) ?></div>
                            <div class="small text-muted"><?= esc($row['area']) ?> · <?= esc($row['subarea']) ?></div>
                            <?php if ($row['nueva']): ?><span class="badge bg-primary bg-opacity-10 text-primary">Nueva (N)</span><?php endif; ?>
                            <?php if ($row['demanda']): ?><span class="badge bg-info bg-opacity-10 text-dark">Mayor demanda (D)</span><?php endif; ?>
                            <?php if ($row['estado'] === 'con_observaciones'): ?>
                                <span class="badge bg-warning text-dark">Con observaciones</span>
                            <?php endif; ?>
                        </td>
                        <td class="small"><?= esc($row['contexto']) ?>
                            <?php if ($row['facturacion']): ?><div class="text-muted">Facturación de empresa</div><?php endif; ?>
                        </td>
                        <td class="small"><strong><?= esc($row['moneda'] ?? 'Por confirmar') ?></strong><br><?= esc(ucfirst($row['periodicidad'])) ?></td>
                        <td class="text-end text-nowrap"><?= esc(salarios_importe($row['minimo'], $row['operador_minimo'])) ?></td>
                        <td class="text-end text-nowrap"><?= esc(salarios_importe($row['medio'])) ?></td>
                        <td class="text-end text-nowrap"><?= esc(salarios_importe($row['maximo'])) ?></td>
                        <td class="small text-nowrap"><?= esc($row['variable_original'] ?? '—') ?></td>
                        <td class="small text-nowrap"><?= esc($row['anio']) ?><br>Pág. <?= (int) $row['pagina'] ?></td>
                        <td class="pe-3"><a class="btn btn-outline-primary btn-sm" href="<?= esc(salarios_url($filtros, [], 'salarios/perfil/' . $row['id']), 'attr') ?>" aria-label="<?= esc('Ver referencia de ' . $row['puesto'], 'attr') ?>">Ver ficha</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$referencias): ?>
                    <tr><td colspan="9" class="text-center py-5 text-muted">
                        <?= $disponible ? 'No se encontraron referencias con estos filtros.' : 'El catálogo salarial todavía no está disponible. Contacta al administrador.' ?>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($total > 0): ?>
        <div class="card-body d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <span class="small text-muted">Mostrando <?= ($pagina - 1) * 25 + 1 ?>–<?= min($pagina * 25, $total) ?> de <?= number_format($total) ?></span>
            <nav aria-label="Páginas de referencias salariales"><ul class="pagination pagination-sm mb-0 flex-wrap">
                <?php if ($pagina > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= esc(salarios_url($filtros, ['page' => 1]), 'attr') ?>" aria-label="Primera página">«</a></li>
                    <li class="page-item"><a class="page-link" href="<?= esc(salarios_url($filtros, ['page' => $pagina - 1]), 'attr') ?>">Anterior</a></li>
                <?php endif; ?>
                <?php for ($p = max(1, $pagina - 2); $p <= min($paginas, $pagina + 2); $p++): ?>
                    <li class="page-item <?= $p === $pagina ? 'active' : '' ?>"><a class="page-link" <?= $p === $pagina ? 'aria-current="page"' : '' ?> href="<?= esc(salarios_url($filtros, ['page' => $p]), 'attr') ?>"><?= $p ?></a></li>
                <?php endfor; ?>
                <?php if ($pagina < $paginas): ?>
                    <li class="page-item"><a class="page-link" href="<?= esc(salarios_url($filtros, ['page' => $pagina + 1]), 'attr') ?>">Siguiente</a></li>
                    <li class="page-item"><a class="page-link" href="<?= esc(salarios_url($filtros, ['page' => $paginas]), 'attr') ?>" aria-label="Última página">»</a></li>
                <?php endif; ?>
            </ul></nav>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('salariosFiltros');
    const input = document.getElementById('salBuscar');
    const container = document.getElementById('salBuscarContenedor');
    const list = document.getElementById('salPuestosSugerencias');
    const status = document.getElementById('salBuscarEstado');
    let timer = null;
    let pending = null;
    let generation = 0;
    let names = [];
    let active = -1;
    let composing = false;

    function closeSuggestions() {
        clearTimeout(timer);
        if (pending) pending.abort();
        pending = null;
        generation++;
        names = [];
        active = -1;
        list.replaceChildren();
        list.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        status.textContent = '';
    }

    function activate(index) {
        active = index;
        Array.from(list.children).forEach(function (option, i) {
            option.classList.toggle('active', i === active);
            option.setAttribute('aria-selected', i === active ? 'true' : 'false');
        });
        const option = list.children[active];
        if (option) {
            input.setAttribute('aria-activedescendant', option.id);
            option.scrollIntoView({block: 'nearest'});
        }
    }

    function choose(index) {
        if (!names[index]) return;
        input.value = names[index];
        closeSuggestions();
        if (form.requestSubmit) form.requestSubmit();
        else form.submit();
    }

    async function loadSuggestions(version) {
        const controller = new AbortController();
        pending = controller;
        status.textContent = 'Buscando puestos…';
        const params = new URLSearchParams(new FormData(form));
        params.set('buscar', input.value.trim());
        params.delete('page');
        const url = new URL(input.dataset.sugerenciasUrl, window.location.href);
        url.search = params.toString();
        try {
            const response = await fetch(url, {
                signal: controller.signal, credentials: 'same-origin',
                headers: {'Accept': 'application/json'}
            });
            if (version !== generation) return;
            if (response.status === 401) {
                status.textContent = 'Tu sesión terminó. Inicia sesión para consultar sugerencias.';
                return;
            }
            if (!response.ok || !(response.headers.get('content-type') || '').includes('application/json')) {
                throw new Error('Sugerencias no disponibles');
            }
            const data = await response.json();
            if (version !== generation) return;
            names = Array.isArray(data.puestos)
                ? Array.from(new Set(data.puestos.filter(function (name) { return typeof name === 'string'; }))).slice(0, 10)
                : [];
            list.replaceChildren();
            names.forEach(function (name, index) {
                const option = document.createElement('li');
                option.id = 'salPuestoOpcion-' + index;
                option.className = 'list-group-item list-group-item-action py-2 small';
                option.setAttribute('role', 'option');
                option.setAttribute('aria-selected', 'false');
                option.style.cursor = 'pointer';
                option.textContent = name;
                option.addEventListener('mousedown', function (event) { event.preventDefault(); });
                option.addEventListener('click', function () { choose(index); });
                list.appendChild(option);
            });
            list.hidden = names.length === 0;
            input.setAttribute('aria-expanded', names.length ? 'true' : 'false');
            status.textContent = names.length
                ? names.length + ' sugerencias. Usa las flechas y Enter para elegir.'
                : 'No hay sugerencias con estos filtros. Puedes escribir otro puesto.';
        } catch (error) {
            if (version !== generation || error.name === 'AbortError') return;
            status.textContent = 'No se pudieron cargar las sugerencias. Puedes escribir el puesto y pulsar Consultar.';
        } finally {
            if (pending === controller) pending = null;
        }
    }

    function scheduleSuggestions() {
        closeSuggestions();
        if (composing || input.value.trim().length < 2) return;
        const version = generation;
        timer = setTimeout(function () { loadSuggestions(version); }, 220);
    }

    input.addEventListener('input', scheduleSuggestions);
    input.addEventListener('focus', scheduleSuggestions);
    input.addEventListener('compositionstart', function () { composing = true; closeSuggestions(); });
    input.addEventListener('compositionend', function () { composing = false; scheduleSuggestions(); });
    input.addEventListener('keydown', function (event) {
        if (composing || event.isComposing) return;
        if (event.key === 'Escape' || event.key === 'Tab') {
            closeSuggestions();
        } else if (!list.hidden && names.length) {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                const next = event.key === 'ArrowDown'
                    ? (active + 1) % names.length
                    : (active <= 0 ? names.length - 1 : active - 1);
                activate(next);
            } else if (event.key === 'Enter' && active >= 0) {
                event.preventDefault();
                choose(active);
            }
        }
    });
    document.addEventListener('pointerdown', function (event) {
        if (!container.contains(event.target)) closeSuggestions();
    });
    document.addEventListener('focusin', function (event) {
        if (!container.contains(event.target)) closeSuggestions();
    });
    window.addEventListener('blur', closeSuggestions);
    form.addEventListener('submit', closeSuggestions);
    form.addEventListener('change', closeSuggestions);
    form.querySelector('#salArea').addEventListener('change', function () {
        form.querySelector('#salSubarea').value = '';
        form.submit();
    });
    form.querySelector('#salGuia').addEventListener('change', function () {
        ['#salArea', '#salSubarea', '#salFacturacion'].forEach(function (selector) {
            form.querySelector(selector).value = '';
        });
        form.submit();
    });
});
</script>
<?= $this->endSection() ?>