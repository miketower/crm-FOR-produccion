<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="<?= base_url('eventos') ?>" class="text-decoration-none text-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Regresar a Eventos</a>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold text-dark mb-0"><?= esc($evento['titulo']) ?></h4>
            <span class="badge <?= $evento['categoria_evento'] == 'Externo' ? 'bg-primary' : 'bg-secondary' ?> fs-6">
                <?= esc($evento['categoria_evento']) ?>
            </span>
            <a href="<?= base_url('eventos/editar/' . $evento['id']) ?>" class="btn btn-outline-primary btn-sm ms-3">
                <i class="bi bi-pencil-square"></i> Editar Evento
            </a>
        </div>
    </div>
    <span class="badge <?= $evento['estatus'] == 'Pendiente' ? 'bg-warning text-dark' : 'bg-success' ?> fs-6">
        <?= esc($evento['estatus']) ?>
    </span>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold"><i class="bi bi-info-circle me-2"></i> Detalles del Evento</div>
            <div class="card-body">
                <p><strong>Objetivo:</strong> <?= esc($evento['objetivo'] ?? 'No definido') ?></p>
                
                <?php if($evento['liga_registro']): ?>
                    <p><strong>Liga de Registro:</strong> <a href="<?= esc($evento['liga_registro']) ?>" target="_blank"><?= esc($evento['liga_registro']) ?></a></p>
                <?php endif; ?>

                <hr class="my-3">

                <!-- DATOS CONDICIONALES SEGÚN CATEGORÍA -->
                <?php if($evento['categoria_evento'] == 'Externo'): ?>
                    <p><strong>Organizador:</strong> <?= esc($evento['organizador']) ?></p>
                    <p><strong>Industria / Segmentación:</strong> <?= esc($evento['industria_segmentacion']) ?></p>
                    <p><strong>Alcance:</strong> <?= esc($evento['alcance']) ?></p>
                <?php else: ?>
                    <p><strong>Responsable FOR:</strong> <?= esc($evento['responsable']) ?></p>
                    <p><strong>Tipo de Evento:</strong> <?= esc($evento['tipo_evento']) ?></p>
                    <p><strong>Invitados:</strong> <?= esc($evento['invitados']) ?></p>
                    <p><strong>Capacidad Asistentes:</strong> <?= esc($evento['capacidad']) ?> personas</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold"><i class="bi bi-calendar-check me-2"></i> Logística</div>
            <div class="card-body">
                <p><i class="bi bi-calendar-event text-primary me-2"></i> <strong>Fecha:</strong><br> <?= date('d/m/Y h:i A', strtotime($evento['fecha_evento'])) ?></p>
                <p><i class="bi bi-geo-alt text-danger me-2"></i> <strong>Ubicación:</strong><br> <?= esc($evento['ubicacion'] ?? 'Por definir') ?></p>
                
                <?php if($evento['categoria_evento'] == 'Externo'): ?>
                    <p><i class="bi bi-cash-coin text-success me-2"></i> <strong>Costo:</strong><br> $<?= number_format($evento['costo'] ?? 0, 2) ?> MXN</p>
                    <hr>
                    <p><strong>Roles FOR que deben asistir:</strong><br>
                        <span class="badge bg-info text-dark"><?= esc($evento['roles_asistencia'] ?: 'No definidos') ?></span>
                    </p>
                <?php else: ?>
                    <p><i class="bi bi-wallet2 text-success me-2"></i> <strong>Presupuesto:</strong><br> $<?= number_format($evento['presupuesto'] ?? 0, 2) ?> MXN</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>