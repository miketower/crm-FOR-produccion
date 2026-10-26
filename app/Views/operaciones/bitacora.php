<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<style>
    /* Estilos sutiles para la interacción de la tabla */
    .kpi-card { border-left: 4px solid #0d6efd; }
    .row-clic { cursor: pointer; transition: background 0.2s; }
    .row-clic:hover { background-color: #f8f9fa; }
    .selected-row { background-color: #e9ecef !important; border-left: 4px solid #0d6efd; }
    .firma-box { border: 1px dashed #ccc; background: #f9f9f9; padding: 15px; text-align: center; border-radius: 8px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Bitácora de Incidencias Operativas</h2>
    <a href="<?= base_url('operaciones/nuevo') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg"></i> Reportar Incidencia
    </a>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('msg') ?>
    </div>
<?php endif; ?>

<!-- 1. KPIs SUPERIORES -->
<?php 
    $total = count($tickets);$pendientesFirma = 0;
    $logrados = 0;
    foreach($tickets as$t) {
        if($t['resultado'] == 'proceso')$pendientesFirma++;
        if($t['resultado'] == 'logrado')$logrados++;
    }
    $porcentajeLogrados =$total > 0 ? round(($logrados / $total) * 100) : 0;
?>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 kpi-card h-100">
            <div class="card-body">
                <h3 class="fw-bold text-dark mb-0"><?= $total ?></h3>
                <span class="text-muted small text-uppercase">Incidencias Totales</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 kpi-card h-100" style="border-left-color: #dc3545;">
            <div class="card-body">
                <h3 class="fw-bold text-danger mb-0"><?= $pendientesFirma ?></h3>
                <span class="text-muted small text-uppercase">Pendientes de Firma</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 kpi-card h-100" style="border-left-color: #198754;">
            <div class="card-body">
                <h3 class="fw-bold text-success mb-0"><?= $porcentajeLogrados ?>%</h3>
                <span class="text-muted small text-uppercase">Acciones Logradas</span>
            </div>
        </div>
    </div>
</div>

<!-- 2. BARRA DE FILTROS -->
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body py-3">
        <form action="<?= base_url('operaciones') ?>" method="GET" class="row g-2 align-items-end" id="formFiltros">
            
            <div class="col-md-3">
                <label class="small text-muted fw-bold">Área</label>
                <select name="area_filtro" class="form-select form-select-sm fw-bold" onchange="document.getElementById('formFiltros').submit()">
                    <option value="">Todas</option>
                    <option value="Logística" <?= isset($_GET['area_filtro']) && $_GET['area_filtro']=='Logística' ? 'selected':'' ?>>Logística</option>
                    <option value="Almacén" <?= isset($_GET['area_filtro']) && $_GET['area_filtro']=='Almacén' ? 'selected':'' ?>>Almacén</option>
                    <option value="Distribución" <?= isset($_GET['area_filtro']) && $_GET['area_filtro']=='Distribución' ? 'selected':'' ?>>Distribución</option>
                    <option value="Ventas" <?= isset($_GET['area_filtro']) && $_GET['area_filtro']=='Ventas' ? 'selected':'' ?>>Ventas</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="small text-muted fw-bold">Tipo de Acción</label>
                <select name="tipo_filtro" class="form-select form-select-sm fw-bold" onchange="document.getElementById('formFiltros').submit()">
                    <option value="">Todos</option>
                    <option value="politica" <?= isset($_GET['tipo_filtro']) && $_GET['tipo_filtro']=='politica' ? 'selected':'' ?>>Política</option>
                    <option value="proceso" <?= isset($_GET['tipo_filtro']) && $_GET['tipo_filtro']=='proceso' ? 'selected':'' ?>>Ajuste a proceso</option>
                    <option value="reglamento" <?= isset($_GET['tipo_filtro']) && $_GET['tipo_filtro']=='reglamento' ? 'selected':'' ?>>Reglamento</option>
                    <option value="iniciativa" <?= isset($_GET['tipo_filtro']) && $_GET['tipo_filtro']=='iniciativa' ? 'selected':'' ?>>Iniciativa</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="small text-muted fw-bold">Desde</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="<?= $fecha_inicio ?>" onchange="document.getElementById('formFiltros').submit()">
            </div>
            
            <div class="col-md-2">
                <label class="small text-muted fw-bold">Hasta</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm" value="<?= $fecha_fin ?>" onchange="document.getElementById('formFiltros').submit()">
            </div>

            <div class="col-md-3 text-end">
                <a href="<?= base_url('operaciones') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- 3. TABLA DE TICKETS -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Folio</th>
                        <th>Qué sucedió</th>
                        <th>Área</th>
                        <th>Responsable</th>
                        <th>Sucedió</th>
                        <th>Tipo de Acción</th>
                        <th>Estado</th>
                        <th>Firma</th>
                        <th class="pe-4 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($tickets)): ?>
                        <?php foreach($tickets as $t): ?>
                            <?php 
                                $tJson = htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8'); 
                            ?>
                            <tr class="row-clic" onclick="mostrarDetalle(<?= $tJson ?>, this)">
                                <td class="ps-4 fw-bold text-muted"><?= esc($t['folio']) ?></td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="<?= esc($t['que_sucedio']) ?>">
                                        <?= esc($t['que_sucedio']) ?>
                                    </div>
                                </td>
                                <td><?= esc($t['area_departamento']) ?></td>
                                <td><?= esc($t['responsable_nombre'] ?? 'Sin asignar') ?></td>
                                <td><?= date('d/m/Y', strtotime($t['fecha_suceso'])) ?></td>
                                
                                <!-- NUEVA COLUMNA: TIPO DE ACCIÓN -->
                                <td>
                                    <?php if($t['tipo_accion'] == 'politica'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Política</span>
                                    <?php elseif($t['tipo_accion'] == 'proceso'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Proceso</span>
                                    <?php elseif($t['tipo_accion'] == 'reglamento'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Reglamento</span>
                                    <?php elseif($t['tipo_accion'] == 'iniciativa'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Iniciativa</span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($t['resultado'] == 'logrado'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Logrado</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">En Proceso</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($t['firma_supervisor_id']): ?>
                                        <span class="text-success small fw-bold"><i class="bi bi-check2-all"></i> Firmado</span>
                                    <?php else: ?>
                                        <span class="text-muted small">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="<?= base_url('operaciones/atender/'.$t['id']) ?>" class="btn btn-outline-primary btn-sm shadow-sm" title="Atender / Firmar" onclick="event.stopPropagation();">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">No hay tickets registrados aún.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 4. PANEL DE DETALLE EXPANDIDO (JS) -->
<div id="panelDetalle" class="card shadow border-0 d-none bg-light border-top border-primary border-4 mb-5">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="fw-bold mb-0">Detalle del caso <span id="det_folio" class="text-primary"></span></h5>
            <button type="button" class="btn-close" onclick="document.getElementById('panelDetalle').classList.add('d-none')"></button>
        </div>
        
        <div class="row g-4">
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="small fw-bold text-muted text-uppercase">Qué sucedió</label>
                        <div id="det_que" class="fs-6"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase">Área</label>
                        <div id="det_area" class="fs-6 fw-bold"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase">Cuándo sucedió</label>
                        <div id="det_fecha_suceso" class="fs-6"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase">Responsable</label>
                        <div id="det_responsable" class="fs-6 fw-bold"></div>
                    </div>
                    <div class="col-12 mt-4">
                        <label class="small fw-bold text-muted text-uppercase">Acciones implementadas</label>
                        <div id="det_acciones" class="fs-6 bg-white p-2 border rounded"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="firma-box h-100 d-flex flex-column justify-content-center">
                    <span class="small fw-bold text-muted text-uppercase mb-2">Cierre y Firma</span>
                    <div id="det_firma_estado"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Lógica para el panel inferior maestro-detalle
    function mostrarDetalle(t, rowElement) {
        // Pintar fila seleccionada
        document.querySelectorAll('.row-clic').forEach(el => el.classList.remove('selected-row'));
        rowElement.classList.add('selected-row');

        // Mostrar panel
        document.getElementById('panelDetalle').classList.remove('d-none');

        // Llenar info
        document.getElementById('det_folio').innerText = t.folio;
        document.getElementById('det_que').innerText = t.que_sucedio;
        document.getElementById('det_area').innerText = t.area_departamento;
        
        let fSuceso = new Date(t.fecha_suceso + 'T00:00:00');
        document.getElementById('det_fecha_suceso').innerText = fSuceso.toLocaleDateString();
        
        document.getElementById('det_responsable').innerText = t.responsable_nombre || 'No asignado';
        document.getElementById('det_acciones').innerText = t.acciones_implementadas || 'Sin documentar aún.';

        // Lógica visual de la firma simulada
        let firmaHtml = '';
        if(t.firma_supervisor_id) {
            let fFirma = new Date(t.fecha_firma);
            firmaHtml = `
                <div class="text-success mb-2">
                    <svg viewBox="0 0 220 70" width="140" height="40"><path d="M10,50 C 30,10 40,60 55,35 S 80,15 95,40 S 115,55 130,30 S 150,10 165,38 S 190,55 205,32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <div class="small fw-bold text-dark">Firmado por ${t.supervisor_nombre}</div>
                <div class="small text-muted">${fFirma.toLocaleString()}</div>
            `;
        } else {
            firmaHtml = `<div class="text-muted small mt-2">Cierre pendiente. <br>Falta firma de supervisor.</div>`;
        }
        document.getElementById('det_firma_estado').innerHTML = firmaHtml;

        // Bajar el scroll al panel suavemente
        document.getElementById('panelDetalle').scrollIntoView({ behavior: 'smooth', block: 'end' });
    }
</script>

<?= $this->endSection() ?>