<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>
<div class="row mt-5">
    <div class="col-md-6 mx-auto">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-spreadsheet"></i> Importar Histórico (CSV)</h5>
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

                <p class="text-muted mb-4">Sube aquí el archivo CSV para alimentar el sistema. Asegúrate de incluir el periodo al que corresponden estos registros.</p>

                <form action="<?= base_url('importar/procesarUnificado') ?>" method="post" enctype="multipart/form-data">
                    
                <div class="mb-3">
                    <label for="periodo" class="form-label fw-bold">Periodo de Carga</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                        <input type="month" name="periodo" class="form-control" required>
                    </div>
                    <small class="text-muted">Selecciona el mes y año para agrupar estas ventas.</small>
                </div>

                <!-- En tu formulario de carga -->
                <div class="mb-3">
                    <label class="form-label">Tipo de Venta (Categoría)</label>
                    <select name="cat_tipo_venta_id" class="form-select" required>
                        <option value="1">Optimización</option>
                        <option value="2">Soluciones</option>
                        <option value="3">Otros</option>
                    </select>
                </div>

                    <div class="mb-4">
                        <label for="csv_file" class="form-label fw-bold">Seleccionar Archivo CSV</label>
                        <input class="form-control" type="file" name="csv_file" accept=".csv" required>
                    </div>

                    <div class="alert alert-warning border-warning d-flex align-items-start mb-4" role="alert">
                        <i class="bi bi-exclamation-circle fs-4 me-3 text-warning"></i>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="wipe_data" value="1" id="wipeDataCheck">
                                <label class="form-check-label fw-bold text-dark" for="wipeDataCheck">
                                    Borrar base de datos actual antes de importar
                                </label>
                            </div>
                            <div class="small text-muted mt-1">
                                ⚠️ Si marcas esta casilla, se eliminarán <strong>TODOS</strong> los prospectos y ventas existentes.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg shadow">
                            Iniciar Importación
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <a href="<?= base_url('prospectos') ?>" class="text-decoration-none text-muted">
                        <i class="bi bi-arrow-left"></i> Volver al Tablero
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>