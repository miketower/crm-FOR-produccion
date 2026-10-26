<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<style>
    .sortable { cursor: pointer; user-select: none; }
    .sortable:hover { background-color: #e9ecef; }
    .sort-icon { font-size: 0.8em; margin-left: 5px; color: #999; }
    .asc .sort-icon::after { content: "▲"; color: #333; }
    .desc .sort-icon::after { content: "▼"; color: #333; }
    /* Por defecto mostramos flechas dobles tenues */
    .sortable .sort-icon::after { content: "⇅"; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Tablero de Prospectos</h2>
    <a href="<?= base_url('prospectos/nuevo') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Nuevo Prospecto
    </a>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('msg') ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body py-3">
        <!-- Unificamos todo en un solo formulario dinámico -->
        <form id="formFiltros" action="<?= base_url('prospectos') ?>" method="GET" class="row g-2 align-items-end">
            
            <!-- Buscador por Texto -->
            <div class="col-md-3">
                <label class="small text-muted fw-bold">Búsqueda (Empresa o Estado)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" name="search" class="form-control form-control-sm border-start-0 ps-0" placeholder="Escribe para filtrar en tiempo real..." value="<?= esc($_GET['search'] ?? '') ?>">
                </div>
            </div>

            <!-- Servicios Dinámicos -->
            <div class="col-md-2">
                <label class="small text-muted fw-bold">Servicio</label>
                <select id="filterService" name="servicio_filtro" class="form-select form-select-sm fw-bold">
                    <option value="">Todos</option>
                    <?php foreach($cat_servicios as $s): ?>
                        <option value="<?= esc($s['name']) ?>" <?= (isset($_GET['servicio_filtro']) && $_GET['servicio_filtro'] == $s['name']) ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Giros/Industria Dinámicos -->
            <div class="col-md-2">
                <label class="small text-muted fw-bold">Giro / Macro</label>
                <select id="filterMacro" name="macro_filtro" class="form-select form-select-sm fw-bold">
                    <option value="">Todos</option>
                    <?php foreach($cat_industrias as $ind): ?>
                        <option value="<?= esc(strtolower($ind['nombre_categoria'])) ?>" <?= (isset($_GET['macro_filtro']) && $_GET['macro_filtro'] == strtolower($ind['nombre_categoria'])) ? 'selected' : '' ?>><?= esc($ind['nombre_categoria']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Etapas Dinámicas -->
            <div class="col-md-1">
                <label class="small text-muted fw-bold">Etapa</label>
                <select id="filterStage" name="etapa_filtro" class="form-select form-select-sm fw-bold">
                    <option value="">Todas</option>
                    <option value="Nuevo" <?= (isset($_GET['etapa_filtro']) && $_GET['etapa_filtro'] == 'Nuevo') ? 'selected' : '' ?>>Nuevo</option>
                    <option value="Contactado" <?= (isset($_GET['etapa_filtro']) && $_GET['etapa_filtro'] == 'Contactado') ? 'selected' : '' ?>>Contactado</option>
                    <option value="Propuesta" <?= (isset($_GET['etapa_filtro']) && $_GET['etapa_filtro'] == 'Propuesta') ? 'selected' : '' ?>>Propuesta</option>
                    <option value="Negociacion" <?= (isset($_GET['etapa_filtro']) && $_GET['etapa_filtro'] == 'Negociacion') ? 'selected' : '' ?>>Negociación</option>
                    <option value="Cerrado Ganado" <?= (isset($_GET['etapa_filtro']) && $_GET['etapa_filtro'] == 'Cerrado Ganado') ? 'selected' : '' ?>>Ganado</option>
                    <option value="Cerrado Perdido" <?= (isset($_GET['etapa_filtro']) && $_GET['etapa_filtro'] == 'Cerrado Perdido') ? 'selected' : '' ?>>Perdido</option>
                    <option value="Cerrado sin decisión" <?= (isset($_GET['etapa_filtro']) && $_GET['etapa_filtro'] == 'Cerrado sin decisión') ? 'selected' : '' ?>>Sin Decisión</option>
                </select>
            </div>

            <!-- Grupo -->
            <div class="col-md-1">
                <label class="small text-muted fw-bold">Grupo</label>
                <select id="filterGroup" name="grupo" class="form-select form-select-sm fw-bold">
                    <option value="">Todos</option>
                    <?php foreach($grupos as $g): ?>
                        <option value="<?= $g ?>" <?= (isset($_GET['grupo']) && $_GET['grupo'] == $g) ? 'selected' : '' ?>>
                            <?= $g ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Rango de Fechas del Servidor -->
            <div class="col-md-1">
                <label class="small text-muted fw-bold">Desde</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="<?= $fecha_inicio ?>">
            </div>
            <div class="col-md-1">
                <label class="small text-muted fw-bold">Hasta</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm" value="<?= $fecha_fin ?>">
            </div>

            <!-- Botones de Acción -->
            <div class="col-md-1 text-end">
                <div class="d-flex gap-1 justify-content-end">
                    <a href="<?= base_url('prospectos') ?>" class="btn btn-outline-secondary btn-sm" title="Limpiar todos los filtros"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold" title="Filtrar base de datos">Reporte</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="card shadow border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="mainTable">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 sortable" data-type="text" onclick="sortTable(0)">
                        Empresa <span class="sort-icon"></span>
                    </th>
                    
                    <th class="py-3 sortable" data-type="text" onclick="sortTable(1)">
                        Servicio <span class="sort-icon"></span>
                    </th>

                    <th class="py-3 sortable" data-type="number" onclick="sortTable(2)">
                        Ticket Prom. <span class="sort-icon"></span>
                    </th>

                    <th class="py-3 sortable" data-type="number" onclick="sortTable(3)">
                        Ingreso Bruto <span class="sort-icon"></span>
                    </th>

                    <th class="py-3 text-center sortable" data-type="number" onclick="sortTable(4)">
                        Comisión <span class="sort-icon"></span>
                    </th>

                    <th class="py-3 text-center sortable" data-type="number" onclick="sortTable(5)" style="width: 15%;">
                    Perfil Ideal <span class="sort-icon"></span>
                    </th>

                    <th class="py-3 sortable" data-type="text" onclick="sortTable(6)">
                        Etapa <span class="sort-icon"></span>
                    </th>

                    <th class="pe-4 py-3 text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php if(!empty($prospectos)): ?>
                    <?php foreach($prospectos as $p): ?>
                        
                        <tr class="prospect-row" 
                            data-company="<?= strtolower($p['company_name']) ?>"
                            data-state="<?= strtolower($p['state_name'] ?? '') ?>"
                            data-service="<?= $p['service_name'] ?? 'Otros' ?>"
                            data-stage="<?= $p['pipeline_stage'] ?>"
                            data-group="<?= $p['classification_group'] ?? 'D' ?>"
                            data-macro="<?= strtolower($p['nombre_macro'] ?? '') ?>">

                            <td class="ps-4" data-sort="<?= strtolower($p['company_name']) ?>">
                                <div class="fw-bold text-dark"><?= esc($p['company_name']) ?></div>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> <?= esc($p['state_name'] ?? 'N/A') ?>
                                </small>
                            </td>

                            <td data-sort="<?= strtolower($p['service_name'] ?? '') ?>">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <?= esc($p['service_name'] ?? 'Sin Servicio') ?>
                                </span>
                            </td>

                            <td class="fw-bold text-dark" data-sort="<?= $p['ticket_value'] ?? 0 ?>">
                                $<?= number_format($p['ticket_value'] ?? 0, 2) ?>
                            </td>

                            <td class="text-success" data-sort="<?= $p['total_ingreso_bruto'] ?? 0 ?>">
                                $<?= number_format($p['total_ingreso_bruto'] ?? 0, 2) ?>
                            </td>

                            <td class="text-center" data-sort="<?= $p['total_comision'] ?? 0 ?>">
                                <?php if(($p['total_comision'] ?? 0) > 0): ?>
                                    <span class="badge bg-light text-dark border">
                                        $<?= number_format($p['total_comision'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center" data-sort="<?= $p['viability_score'] ?? 0 ?>">
                                <?php 
                                    $score = $p['viability_score'] ?? 0;
                                    // Colores por rango numérico
                                    if ($score >= 70) { $textClass = 'text-success'; $barClass = 'bg-success'; }
                                    elseif ($score >= 40) { $textClass = 'text-warning'; $barClass = 'bg-warning'; }
                                    else { $textClass = 'text-danger'; $barClass = 'bg-danger'; }
                                ?>
                                <div class="fw-bold fs-5 <?= $textClass ?>">
                                    <?= $score ?>%
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $score ?>%"></div>
                                </div>
                            </td>

                            <td data-sort="<?= strtolower($p['pipeline_stage']) ?>">
                                <?php 
                                    $stage = $p['pipeline_stage'];
                                    $styleClass = 'text-muted'; $icon = '';

                                    // Azul: Activos
                                    if (in_array($stage, ['Nuevo', 'Contactado', 'Propuesta', 'Negociacion', 'Negociación'])) {
                                        $styleClass = 'text-primary fw-bold';
                                        $icon = '<i class="bi bi-circle-fill small me-1" style="font-size: 0.5rem;"></i>';
                                    }
                                    // Verde: Ganado
                                    elseif (stripos($stage, 'Ganado') !== false) {
                                        $styleClass = 'text-success fw-bold';
                                        $icon = '<i class="bi bi-check-circle-fill me-1"></i>';
                                    }
                                    // Rojo: Perdido
                                    elseif (stripos($stage, 'Perdido') !== false) {
                                        $styleClass = 'text-danger fw-bold';
                                        $icon = '<i class="bi bi-x-circle-fill me-1"></i>';
                                    }
                                    // Gris: Indeciso
                                    elseif (stripos($stage, 'decisi') !== false) {
                                        $styleClass = 'text-secondary';
                                        $icon = '<i class="bi bi-dash-circle me-1"></i>';
                                    }
                                ?>
                                <span class="<?= $styleClass ?>">
                                    <?= $icon ?><?= esc($stage) ?>
                                </span>
                            </td>

                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= base_url('prospectos/editar/'.$p['id']) ?>" class="btn btn-outline-secondary btn-sm shadow-sm" title="Editar Prospecto">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <a href="<?= base_url('/prospectos/perfil/' . $p['id']) ?>" class="btn btn-outline-info btn-sm shadow-sm" title="Ver Muro de Actividades">
                                        <i class="bi bi-person-lines-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="emptyRow">
                        <td colspan="8" class="text-center py-5 text-muted">
                            No hay prospectos registrados aún.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <!-- En tu archivo listado.php, donde imprimes el paginador -->
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                <?= $pager ?>
            </nav>
        </div>
        
        <div id="noResults" class="text-center py-5 text-muted d-none">
            <i class="bi bi-search fs-1 d-block mb-3"></i>
            No se encontraron resultados con estos filtros.
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DE FILTRADO (Buscador y Selects) ---
    // --- LÓGICA DE FILTRADO (Autoguardado / Submit al cambiar) ---
    document.addEventListener('DOMContentLoaded', function() {
        const formFiltros = document.getElementById('formFiltros');
        const selects = formFiltros.querySelectorAll('select');
        const searchInput = document.getElementById('searchInput');

        // Si cambian un dropdown, hacer submit del formulario para filtrar toda la BD
        selects.forEach(select => {
            select.addEventListener('change', function() {
                formFiltros.submit();
            });
        });

        // Opcional: Hacer que la barra de búsqueda aplique el filtro al presionar "Enter"
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evitamos un doble submit
                formFiltros.submit();
            }
        });
    });

    // --- LÓGICA DE ORDENAMIENTO (Sorting) ---
    let currentColumn = -1;
    let sortDirection = 'asc'; // 'asc' o 'desc'

    function sortTable(columnIndex) {
        const tableBody = document.getElementById("tableBody");
        const rowsArray = Array.from(tableBody.querySelectorAll(".prospect-row"));
        const headers = document.querySelectorAll("th.sortable");
        
        // 1. Manejo de Flechas Visuales
        headers.forEach(th => {
            th.classList.remove("asc", "desc"); // Limpiar todas
        });

        // 2. Determinar dirección
        if (currentColumn === columnIndex) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortDirection = 'asc';
            currentColumn = columnIndex;
        }

        // 3. Pintar flecha actual
        headers[columnIndex].classList.add(sortDirection);

        // 4. Obtener tipo de dato (texto o numero)
        const type = headers[columnIndex].getAttribute("data-type");

        // 5. Función de Ordenamiento
        rowsArray.sort((a, b) => {
            // Buscamos el valor en el atributo data-sort de la celda correspondiente
            const cellA = a.getElementsByTagName("td")[columnIndex].getAttribute("data-sort");
            const cellB = b.getElementsByTagName("td")[columnIndex].getAttribute("data-sort");

            let valA = cellA;
            let valB = cellB;

            // Convertir a número si es necesario
            if (type === 'number') {
                valA = parseFloat(valA) || 0;
                valB = parseFloat(valB) || 0;
                return sortDirection === 'asc' ? valA - valB : valB - valA;
            } else {
                // Texto
                valA = valA ? valA.toLowerCase() : '';
                valB = valB ? valB.toLowerCase() : '';
                return sortDirection === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
            }
        });

        // 6. Reordenar el DOM (Mover las filas)
        rowsArray.forEach(row => tableBody.appendChild(row));
    }
</script>

<?= $this->endSection() ?>