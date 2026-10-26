<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<style>
    .sortable { cursor: pointer; user-select: none; }
    .sortable:hover { background-color: #e9ecef; }
    .sort-icon { font-size: 0.8em; margin-left: 5px; color: #999; }
    .asc .sort-icon::after { content: "▲"; color: #333; }
    .desc .sort-icon::after { content: "▼"; color: #333; }
    .sortable .sort-icon::after { content: "⇅"; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Cotizaciones (FOR)</h2>
        <p class="text-muted small">Historial de propuestas y proyecciones generadas.</p>
    </div>
    <a href="<?= base_url('cotizador/nuevo') ?>" class="btn btn-primary shadow-sm fw-bold">
        <i class="bi bi-plus-lg me-1"></i> Nueva Cotización
    </a>
</div>

<!-- BARRA DE FILTROS -->
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body py-3">
        <form id="formFiltros" action="<?= base_url('cotizador') ?>" method="GET" class="row g-2 align-items-end">
            
            <div class="col-md-3">
                <label class="small text-muted fw-bold">Búsqueda (Cliente o Solicitante)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" name="search" class="form-control form-control-sm border-start-0 ps-0" placeholder="Escribe para buscar..." value="<?= esc($_GET['search'] ?? '') ?>">
                </div>
            </div>

            <div class="col-md-2">
                <label class="small text-muted fw-bold">Plan Requerido</label>
                <select id="filterPlan" name="plan_filtro" class="form-select form-select-sm fw-bold">
                    <option value="">Todos</option>
                    <option value="Proem" <?= (isset($_GET['plan_filtro']) && $_GET['plan_filtro'] == 'Proem') ? 'selected' : '' ?>>Proem</option>
                    <option value="Servicio Especializado" <?= (isset($_GET['plan_filtro']) && $_GET['plan_filtro'] == 'Servicio Especializado') ? 'selected' : '' ?>>Servicio Especializado</option>
                    <option value="Mixto" <?= (isset($_GET['plan_filtro']) && $_GET['plan_filtro'] == 'Mixto') ? 'selected' : '' ?>>Mixto</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="small text-muted fw-bold">Nivel de Riesgo</label>
                <select id="filterRiesgo" name="riesgo_filtro" class="form-select form-select-sm fw-bold">
                    <option value="">Todos</option>
                    <option value="0" <?= (isset($_GET['riesgo_filtro']) && $_GET['riesgo_filtro'] === '0') ? 'selected' : '' ?>>Bajo (0)</option>
                    <option value="1" <?= (isset($_GET['riesgo_filtro']) && $_GET['riesgo_filtro'] == '1') ? 'selected' : '' ?>>Medio (1)</option>
                    <option value="2" <?= (isset($_GET['riesgo_filtro']) && $_GET['riesgo_filtro'] == '2') ? 'selected' : '' ?>>Alto (2)</option>
                    <option value="3" <?= (isset($_GET['riesgo_filtro']) && $_GET['riesgo_filtro'] == '3') ? 'selected' : '' ?>>Crítico (3)</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="small text-muted fw-bold">Desde</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="<?= $fecha_inicio ?>">
            </div>
            
            <div class="col-md-2">
                <label class="small text-muted fw-bold">Hasta</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm" value="<?= $fecha_fin ?>">
            </div>

            <div class="col-md-1 text-end">
                <div class="d-flex gap-1 justify-content-end">
                    <a href="<?= base_url('cotizador') ?>" class="btn btn-outline-secondary btn-sm" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TABLA DE RESULTADOS -->
<div class="card shadow-sm border-0 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="mainTable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 sortable" data-type="number" onclick="sortTable(0)">ID <span class="sort-icon"></span></th>
                        <th class="py-3 sortable" data-type="text" onclick="sortTable(1)">Cliente <span class="sort-icon"></span></th>
                        <th class="py-3 sortable" data-type="text" onclick="sortTable(2)">Solicitante <span class="sort-icon"></span></th>
                        <th class="py-3 text-center sortable" data-type="number" onclick="sortTable(3)">Riesgo <span class="sort-icon"></span></th>
                        <th class="py-3 text-center sortable" data-type="text" onclick="sortTable(4)">Plan <span class="sort-icon"></span></th>
                        <th class="py-3 text-center sortable" data-type="number" onclick="sortTable(5)">Comisión <span class="sort-icon"></span></th>
                        <th class="pe-4 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if(!empty($propuestas)): ?>
                        <?php foreach($propuestas as $p): ?>
                            <tr class="prospect-row">
                                <td class="ps-4" data-sort="<?= $p['id'] ?>">
                                    <div class="fw-bold text-dark">#<?= esc($p['id']) ?></div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($p['fecha_creacion'])) ?></small>
                                </td>
                                <td data-sort="<?= strtolower($p['nombre_cliente']) ?>">
                                    <div class="fw-bold text-primary"><?= esc($p['nombre_cliente']) ?></div>
                                </td>
                                <td data-sort="<?= strtolower($p['solicitante']) ?>">
                                    <?= esc($p['solicitante']) ?><br>
                                    <small class="text-muted"><?= esc($p['cargo_solicitante']) ?></small>
                                </td>
                                <td class="text-center" data-sort="<?= $p['nivel_riesgo'] ?>">
                                    <?php 
                                        $riesgoColors = ['0' => 'success', '1' => 'warning', '2' => 'orange', '3' => 'danger'];
                                        $color = $riesgoColors[$p['nivel_riesgo']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> border border-<?= $color ?>">
                                        Nivel <?= esc($p['nivel_riesgo']) ?>
                                    </span>
                                </td>
                                <td class="text-center" data-sort="<?= strtolower($p['plan_requerido']) ?>">
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($p['plan_requerido'] ?: 'No especificado') ?>
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-dark" data-sort="<?= $p['comision_pct'] ?>">
                                    <?= esc($p['comision_pct']) ?>%
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= base_url('cotizador/ver/'.$p['id']) ?>" class="btn btn-outline-info btn-sm shadow-sm" title="Ver Resumen">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('cotizador/editar/'.$p['id']) ?>" class="btn btn-outline-secondary btn-sm shadow-sm" title="Editar Cotización">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-search fs-1 d-block mb-3"></i>
                                No se encontraron cotizaciones con estos filtros.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Render del Paginador -->
        <div class="d-flex justify-content-center mt-4 mb-3">
            <nav aria-label="Page navigation">
                <?= $pager ?>
            </nav>
        </div>

    </div>
</div>

<script>
    // --- Autoguardado de Filtros ---
    document.addEventListener('DOMContentLoaded', function() {
        const formFiltros = document.getElementById('formFiltros');
        const selects = formFiltros.querySelectorAll('select');
        const searchInput = document.getElementById('searchInput');

        selects.forEach(select => {
            select.addEventListener('change', function() {
                formFiltros.submit();
            });
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                formFiltros.submit();
            }
        });
    });

    // --- Ordenamiento Dinámico de Columnas ---
    let currentColumn = -1;
    let sortDirection = 'asc';

    function sortTable(columnIndex) {
        const tableBody = document.getElementById("tableBody");
        const rowsArray = Array.from(tableBody.querySelectorAll(".prospect-row"));
        if(rowsArray.length === 0) return;

        const headers = document.querySelectorAll("th.sortable");
        headers.forEach(th => th.classList.remove("asc", "desc"));

        if (currentColumn === columnIndex) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortDirection = 'asc';
            currentColumn = columnIndex;
        }

        headers[columnIndex].classList.add(sortDirection);
        const type = headers[columnIndex].getAttribute("data-type");

        rowsArray.sort((a, b) => {
            let valA = a.getElementsByTagName("td")[columnIndex].getAttribute("data-sort");
            let valB = b.getElementsByTagName("td")[columnIndex].getAttribute("data-sort");

            if (type === 'number') {
                valA = parseFloat(valA) || 0;
                valB = parseFloat(valB) || 0;
                return sortDirection === 'asc' ? valA - valB : valB - valA;
            } else {
                valA = valA ? valA.toLowerCase() : '';
                valB = valB ? valB.toLowerCase() : '';
                return sortDirection === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
            }
        });

        rowsArray.forEach(row => tableBody.appendChild(row));
    }
</script>

<?= $this->endSection() ?>