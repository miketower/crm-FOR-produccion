<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<div class="card shadow border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><?= esc($socio['nombre']) ?></h4>
            <a href="<?= base_url('socios/editar/' . $socio['id']) ?>" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Editar Asociado FOR
            </a>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <p class="text-muted mb-1 small">Semáforo</p>
                <?php if($socio['semaforo']): ?>
                    <span class="badge" style="background-color: <?= esc($socio['color_hex']) ?>;">
                        <?= esc($socio['semaforo']) ?>
                    </span>
                <?php else: ?>
                    <span class="text-muted small">N/A</span>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1 small">Celular</p>
                <p class="fw-bold"><?= esc($socio['celular'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1 small">Invitado por (Red)</p>
                <p class="fw-bold"><?= esc($socio['red_nombre'] ?? 'Líder / Directo') ?></p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <p class="text-muted mb-1 small">Fecha de Ingreso</p>
                <p class="fw-bold"><?= esc($socio['fecha_entrada'] ?? 'N/A') ?></p>
            </div>
            
            <div class="col-md-3">
                <p class="text-muted mb-1 small">Servicios que comisiona</p>
                <p class="fw-bold"><?= esc($socio['servicios_comisiona'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1 small">Tipo de Participación</p>
                <p class="fw-bold"><?= esc($socio['tipo_participacion'] ?? 'N/A') ?></p>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= base_url('socios') ?>" class="btn btn-secondary">Regresar al listado</a>
        </div>
    </div>
</div>

<h5 class="fw-bold mb-3">Red Directa (Invitados por <?= esc($socio['nombre']) ?>)</h5>
<div class="card shadow border-0">
    <div class="card-body p-0">
        <?php if (!empty($invitados)): ?>
            <ul class="list-group list-group-flush">
                <?php foreach($invitados as $invitado): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('socios/show/' . $invitado['id']) ?>" class="text-decoration-none text-dark fw-bold">
                            <?= esc($invitado['nombre']) ?>
                        </a>
                        <?php if($invitado['semaforo']): ?>
                            <span class="badge" style="background-color: <?= esc($invitado['color_hex']) ?>;">
                                <?= esc($invitado['semaforo']) ?>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="p-4 text-muted small">Este asociado FOR aún no tiene invitados en su red.</div>
        <?php endif; ?>
    </div>
</div>

<hr class="my-4">
<h5 class="fw-bold mb-3 text-primary"><i class="bi bi-file-earmark-text me-2"></i>Gestión de Convenio</h5>
<div class="card shadow-sm border-0 bg-light">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <p class="text-muted mb-1 small">Estatus Convenio</p>
                <p class="fw-bold"><?= esc($socio['estatus_convenio'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1 small">Fecha de Firma</p>
                <p class="fw-bold"><?= esc($socio['fecha_firma'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1 small">Responsable Resguardo</p>
                <p class="fw-bold"><?= esc($socio['responsable_resguardo'] ?? 'N/A') ?></p>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <p class="text-muted mb-1 small">Ubicación Física</p>
                <p class="fw-bold"><?= esc($socio['ubicacion_fisica'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6">
                <p class="text-muted mb-1 small">Ubicación Digital</p>
                <p class="fw-bold"><?= esc($socio['ubicacion_digital'] ?? 'N/A') ?></p>
            </div>
        </div>
        <div class="mt-2">
            <p class="text-muted mb-1 small">Notas</p>
            <p class="fw-bold"><?= nl2br(esc($socio['notas'] ?? 'Sin notas')) ?></p>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?= base_url('socios') ?>" class="btn btn-secondary">Regresar al listado</a>
</div>

<?= $this->endSection() ?>