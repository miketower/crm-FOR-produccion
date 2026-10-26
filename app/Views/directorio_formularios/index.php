<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-journal-code text-primary me-2"></i>Directorio de Formularios</h3>
        <small class="text-muted">Acceso centralizado a formularios web, paneles de administración y credenciales</small>
    </div>
    <div style="width: 280px;">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="filtroFormularios" class="form-control border-start-0" placeholder="Buscar formulario...">
        </div>
    </div>
</div>

<div class="row g-3" id="contenedorTarjetas">
    <?php foreach ($formularios as $f): ?>
        <div class="col-md-6 col-lg-4 tarjeta-item">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 small">
                                <?= esc($f['categoria']) ?>
                            </span>
                            <?php if ($f['password']): ?>
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">
                                    <i class="bi bi-key-fill text-warning me-1"></i> Con Clave
                                </span>
                            <?php endif; ?>
                        </div>

                        <h6 class="fw-bold text-dark mb-3 card-title-text">
                            <?= esc($f['nombre']) ?>
                        </h6>
                    </div>

                    <div>
                        <?php if ($f['password']): ?>
                            <div class="alert alert-warning py-1 px-2 mb-3 d-flex align-items-center justify-content-between small">
                                <div>
                                    <i class="bi bi-lock-fill me-1"></i> <strong>Contraseña:</strong> 
                                    <code class="fw-bold text-dark fs-6 ms-1"><?= esc($f['password']) ?></code>
                                </div>
                                <button class="btn btn-sm btn-link text-dark p-0" onclick="navigator.clipboard.writeText('<?= esc($f['password']) ?>'); alert('¡Contraseña copiada!');" title="Copiar contraseña">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <?php if ($f['formulario']): ?>
                                <a href="<?= esc($f['formulario']) ?>" target="_blank" class="btn btn-outline-primary btn-sm d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-ui-checks me-2"></i>Ir al Formulario</span>
                                    <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($f['panel']): ?>
                                <a href="<?= esc($f['panel']) ?>" target="_blank" class="btn btn-outline-dark btn-sm d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-speedometer2 me-2"></i>Panel / Resultados</span>
                                    <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Filtro dinámico en tiempo real
    document.getElementById('filtroFormularios').addEventListener('keyup', function() {
        let busqueda = this.value.toLowerCase();
        let tarjetas = document.querySelectorAll('.tarjeta-item');

        tarjetas.forEach(function(tarjeta) {
            let texto = tarjeta.innerText.toLowerCase();
            if (texto.includes(busqueda)) {
                tarjeta.style.display = '';
            } else {
                tarjeta.style.display = 'none';
            }
        });
    });
</script>

<?= $this->endSection() ?>