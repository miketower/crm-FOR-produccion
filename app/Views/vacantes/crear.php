<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="container py-4" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Alta de Nueva Vacante</h3>
        <a href="<?= base_url('vacantes') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <form action="<?= base_url('vacantes/guardar') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-bold">Nombre / Título de la Vacante <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="titulo_vacante" placeholder="Ej. Gerente de Operaciones" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Empresa Cliente <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="empresa_cliente" placeholder="Ej. For Human Capital" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Consultor Asignado <span class="text-danger">*</span></label>
                    <select class="form-select" name="consultor" required>
                        <option value="Neto">Neto</option>
                        <option value="Carlos">Carlos</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Fecha de Apertura</label>
                    <input type="date" class="form-control" name="fecha_apertura" value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success fw-bold py-2">
                    <i class="bi bi-check-circle me-1"></i> Registrar Vacante
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>