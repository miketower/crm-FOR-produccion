<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark">Reportar Nueva Incidencia Operativa</h2>
    <a href="<?= base_url('operaciones') ?>" class="btn btn-outline-secondary shadow-sm">
        <i class="bi bi-arrow-left"></i> Volver a la Bitácora
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        
        <form action="<?= base_url('operaciones/guardar') ?>" method="post">
            <?= csrf_field() ?>

            <h5 class="section-title mt-0 text-primary border-bottom pb-2 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> 1. Datos del Suceso
            </h5>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Área / Departamento <span class="text-danger">*</span></label>
                    <select name="area_departamento" class="form-select" required>
                        <option value="">Seleccione el área afectada...</option>
                        <option value="Logística">Logística</option>
                        <option value="Almacén">Almacén</option>
                        <option value="Distribución">Distribución</option>
                        <option value="Ventas">Ventas</option>
                        <option value="Sistemas">Sistemas</option>
                    </select>
                </div>

                <!-- NUEVO CAMPO: ASIGNAR RESPONSABLE DESDE LA CREACIÓN -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Asignar a / Responsable <span class="text-danger">*</span></label>
                    <select name="responsable_id" class="form-select" required>
                        <option value="">Seleccione quién atenderá...</option>
                        <?php foreach($usuarios as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= esc($u['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Fecha del Suceso <span class="text-danger">*</span></label>
                    <!-- Limitamos max a la fecha actual para que no reporten al futuro -->
                    <input type="date" name="fecha_suceso" class="form-control" max="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">¿Qué sucedió? <span class="text-danger">*</span></label>
                    <textarea name="que_sucedio" class="form-control" rows="4" required placeholder="Describe el hecho de forma clara, objetiva y sin juicios de valor..."></textarea>
                    <div class="form-text">Solo describe el incidente. Operaciones se encargará de determinar la causa y responsables.</div>
                </div>
            </div>

            <div class="alert alert-light border mt-4 mb-4 text-muted">
                <i class="bi bi-info-circle-fill me-2 text-primary"></i> 
                Al registrar esta incidencia, se generará un ticket que pasará al equipo de Operaciones para su documentación y resolución.
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                    <i class="bi bi-send-fill me-2"></i> Crear Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>