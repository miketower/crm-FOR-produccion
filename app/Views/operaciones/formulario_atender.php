<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Atender Ticket: <?= esc($ticket['folio']) ?></h2>
        <span class="badge bg-<?= $ticket['resultado'] == 'logrado' ? 'success' : 'warning text-dark' ?> mt-1">
            Estado: <?= strtoupper($ticket['resultado']) ?>
        </span>
    </div>
    <a href="<?= base_url('operaciones') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left"></i> Regresar
    </a>
</div>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm"><i class="bi bi-x-circle-fill me-2"></i> <?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<form action="<?= base_url('operaciones/actualizar/'.$ticket['id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- COLUMNA IZQUIERDA: Datos Originales (Solo lectura) -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-muted mb-3"><i class="bi bi-file-earmark-text me-2"></i> 1. Reporte Original</h6>
                    
                    <div class="mb-3">
                        <label class="small text-muted fw-bold d-block">Área / Departamento</label>
                        <span class="fs-6"><?= esc($ticket['area_departamento']) ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold d-block">Fecha del suceso</label>
                        <span class="fs-6"><?= date('d/m/Y', strtotime($ticket['fecha_suceso'])) ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold d-block">¿Qué sucedió?</label>
                        <p class="fs-6 bg-white p-2 border rounded mt-1"><?= esc($ticket['que_sucedio']) ?></p>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        Registrado el <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Documentación y Firma -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="section-title mt-0 text-primary border-bottom pb-2 mb-4">
                        <i class="bi bi-tools me-2"></i> 2. Documentación Operativa
                    </h5>
                    
                    <div class="row g-3">
                        <!-- RESPONSABLE DIRECTO -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Responsable Directo</label>
                            <select name="responsable_id" id="responsable_id" class="form-select" onchange="mostrarSupervisor('responsable')" <?= $ticket['resultado'] == 'logrado' ? 'disabled' : 'required' ?>>
                                <option value="">Seleccione...</option>
                                <?php foreach($usuarios as$u): ?>
                                    <option value="<?= $u['id'] ?>" 
                                            data-supervisor="<?= esc($u['supervisor_nombre'] ?? '') ?>"
                                            <?= ($ticket['responsable_id'] ==$u['id']) ? 'selected' : '' ?>>
                                        <?= esc($u['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <!-- Muestra el supervisor si el responsable lo tiene -->
                            <div id="info_supervisor_responsable" class="mt-2 small">
                                <?php if(!empty($ticket['supervisor_responsable_nombre'])): ?>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-person-badge text-primary me-1"></i> Supervisor: <?= esc($ticket['supervisor_responsable_nombre']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if($ticket['resultado'] == 'logrado'): ?>
                                <input type="hidden" name="responsable_id" value="<?= $ticket['responsable_id'] ?>">
                            <?php endif; ?>
                        </div>

                        <!-- EJECUTOR DE LA INCIDENCIA -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ejecutor de la incidencia</label>
                            <select name="ejecutor_id" id="ejecutor_id" class="form-select" onchange="mostrarSupervisor('ejecutor')" <?= $ticket['resultado'] == 'logrado' ? 'disabled' : 'required' ?>>
                                <option value="">Seleccione...</option>
                                <?php foreach($usuarios as$u): ?>
                                    <option value="<?= $u['id'] ?>" 
                                            data-supervisor="<?= esc($u['supervisor_nombre'] ?? '') ?>"
                                            <?= ($ticket['ejecutor_id'] ==$u['id']) ? 'selected' : '' ?>>
                                        <?= esc($u['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Muestra el supervisor si el ejecutor lo tiene -->
                            <div id="info_supervisor_ejecutor" class="mt-2 small">
                                <?php if(!empty($ticket['supervisor_ejecutor_nombre'])): ?>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-person-badge text-primary me-1"></i> Supervisor: <?= esc($ticket['supervisor_ejecutor_nombre']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if($ticket['resultado'] == 'logrado'): ?>
                                <input type="hidden" name="ejecutor_id" value="<?= $ticket['ejecutor_id'] ?>">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12 mt-4">
                            <label class="form-label fw-bold">Acciones Implementadas</label>
                            <textarea name="acciones_implementadas" class="form-control" rows="3" <?= $ticket['resultado'] == 'logrado' ? 'readonly' : 'required' ?>><?= esc($ticket['acciones_implementadas']) ?></textarea>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold">¿Qué se logró?</label>
                            <input type="text" name="que_se_logro" class="form-control" value="<?= esc($ticket['que_se_logro']) ?>" <?= $ticket['resultado'] == 'logrado' ? 'readonly' : 'required' ?>>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-bold">Fecha de la Acción</label>
                            <input type="date" name="fecha_accion" class="form-control" value="<?= $ticket['fecha_accion'] ?>" <?= $ticket['resultado'] == 'logrado' ? 'readonly' : 'required' ?>>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-bold">Tipo de Acción / Cierre</label>
                            <select name="tipo_accion" class="form-select" <?= $ticket['resultado'] == 'logrado' ? 'disabled' : 'required' ?>>
                                <option value="">Seleccione...</option>
                                <option value="politica" <?= ($ticket['tipo_accion'] == 'politica') ? 'selected' : '' ?>>Política</option>
                                <option value="proceso" <?= ($ticket['tipo_accion'] == 'proceso') ? 'selected' : '' ?>>Ajuste a proceso</option>
                                <option value="reglamento" <?= ($ticket['tipo_accion'] == 'reglamento') ? 'selected' : '' ?>>Reglamento</option>
                                <option value="iniciativa" <?= ($ticket['tipo_accion'] == 'iniciativa') ? 'selected' : '' ?>>Iniciativa</option>
                            </select>
                            <?php if($ticket['resultado'] == 'logrado'): ?>
                                <input type="hidden" name="tipo_accion" value="<?= $ticket['tipo_accion'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Firma (Solo visible si no está firmado) -->
            <?php if($ticket['resultado'] == 'proceso'): ?>
                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-pen me-2"></i> 3. Cierre y Firma</h6>
                                <span class="small text-muted">Solo jefaturas pueden firmar para cerrar definitivamente el ticket.</span>
                            </div>
                            <!-- Verificamos si el usuario actual es un manager/admin para dejarle firmar -->
                            <?php if(in_array(session()->get('role'), ['admin', 'manager'])): ?>
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" role="switch" name="firmar_ticket" id="firmarSwitch" value="1">
                                    <label class="form-check-label fs-6 mt-1" for="firmarSwitch">Aprobar y Firmar Cierre</label>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-secondary">Requiere firma de supervisor</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg shadow px-5">Guardar Cambios</button>
                </div>

            <?php else: ?>
                <!-- Ticket Cerrado -->
                <div class="alert alert-success d-flex align-items-center shadow-sm">
                    <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-0">Ticket cerrado y firmado</h6>
                        <small>El incidente ha sido resuelto y es parte de la bitácora directiva.</small>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</form>

<script>
    // Actualización dinámica en el frontend del supervisor al seleccionar un usuario
    function mostrarSupervisor(tipo) {
        const select = document.getElementById(tipo + '_id');
        const container = document.getElementById('info_supervisor_' + tipo);
        if (!select || !container) return;

        const selectedOption = select.options[select.selectedIndex];
        const supervisorNombre = selectedOption.getAttribute('data-supervisor');

        if (supervisorNombre && supervisorNombre.trim() !== '') {
            container.innerHTML = `
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-person-badge text-primary me-1"></i> Supervisor: ${supervisorNombre}
                </span>`;
        } else {
            container.innerHTML = '';
        }
    }
</script>

<?= $this->endSection() ?>