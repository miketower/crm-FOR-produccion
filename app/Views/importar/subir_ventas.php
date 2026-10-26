<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>
<div class="row mt-5">
    <div class="col-md-6 mx-auto">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-spreadsheet"></i> Carga Masiva Universal de Ventas</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success shadow-sm">
                        <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <p class="text-muted mb-4">Selecciona el tipo de venta correspondiente y sube el archivo CSV para alimentar los flujos financieros.</p>

                <form action="<?= base_url('importar/procesarVentasCsv') ?>" method="post" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label for="cat_tipo_venta_id" class="form-label fw-bold">1. Tipo de Venta / Canal</label>
                        <select class="form-select" name="cat_tipo_venta_id" id="cat_tipo_venta_id" required>
                            <option value="" selected disabled>-- Selecciona el canal comercial --</option>
                            <option value="1">Optimización</option>
                            <option value="2">Soluciones</option>
                            <option value="3">Otros</option>
                        </select>
                        <div class="form-text">Indica a qué división pertenecen los registros de este archivo.</div>
                    </div>
                    <div class="mb-3">
                        <label for="periodo_retroactivo" class="form-label fw-bold">
                            <i class="bi bi-calendar-month-fill me-1 text-primary"></i> Mes de la Información:
                        </label>
                        <input type="month" 
                            name="periodo_retroactivo" 
                            id="periodo_retroactivo" 
                            class="form-control" 
                            value="<?= date('Y-m'); ?>" 
                            required>
                        <div class="form-text">
                            Selecciona el mes real al que pertenece este reporte (Ej: si subes Marzo hoy, selecciona Marzo).
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-bold">2. Seleccionar Archivo CSV (.csv)</label>
                        <input class="form-control" type="file" name="csv_file" accept=".csv" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg shadow">
                            Iniciar Importación Masiva
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <a href="<?= base_url('ventas') ?>" class="text-decoration-none text-muted">
                        <i class="bi bi-arrow-left"></i> Volver a Ventas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>