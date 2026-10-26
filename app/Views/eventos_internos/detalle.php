<?= $this->extend('layout/main') ?> <!--[cite: 4] -->
<?= $this->section('contenido') ?> <!--[cite: 4] -->

<div class="mb-4 d-flex justify-content-between align-items-center"> <!--[cite: 4] -->
    <div>
        <!-- Ajustamos la URL de regreso -->
        <a href="<?= base_url('eventos_internos') ?>" class="text-decoration-none text-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Regresar a Eventos Internos</a>
        <h4 class="fw-bold text-dark mb-0"><?= esc($evento['titulo']) ?></h4> <!--[cite: 4] -->
    </div>
    <span class="badge <?= $evento['estatus'] == 'Planeación' ? 'bg-warning text-dark' : 'bg-success' ?> fs-6"> <!--[cite: 4] -->
        <?= esc($evento['estatus']) ?> <!--[cite: 4] -->
    </span>
</div>

<div class="row"> <!--[cite: 4] -->
    <div class="col-md-8"> <!--[cite: 4] -->
        <div class="card shadow-sm border-0 mb-4"> <!--[cite: 4] -->
            <div class="card-header bg-white fw-bold"><i class="bi bi-info-circle me-2"></i> Detalles del Evento</div> <!--[cite: 4] -->
            <div class="card-body"> <!--[cite: 4] -->
            <p><strong>Responsable (Anfitrión):</strong> <?= esc($evento['responsable']) ?></p>
            <p><strong>Objetivo:</strong> <?= esc($evento['objetivo']) ?></p>
            <p><strong>Tipo de Evento:</strong> <?= esc($evento['tipo_evento']) ?></p>
            <p><strong>Tipo de Asistente:</strong> <?= esc($evento['tipo_asistente'] ?: 'N/A') ?></p>
            <p><strong>Industria Dirigida:</strong> <?= esc($evento['industria_dirigido'] ?: 'N/A') ?></p>
            <p><strong>Capacidad Estimada:</strong> <?= esc($evento['capacidad']) ?> personas</p>

            <?php if($evento['liga_registro']): ?>
                <p><strong>Liga de Confirmación/RSVP:</strong> <a href="<?= esc($evento['liga_registro']) ?>" target="_blank">Enlace de RSVP</a></p>
            <?php endif; ?>

            <!-- MOSTRAR EL FLYER SI EXISTE -->
            <?php if(!empty($evento['flyer'])): ?>
                <hr>
                <h6 class="fw-bold mt-4"><i class="bi bi-image"></i> Flyer del Evento:</h6>
                <div class="mt-2 text-center p-3 bg-light rounded border">
                    <?php 
                    // Revisamos si el archivo es un PDF para mostrar un botón, o una imagen para mostrarla directamente
                    $ext = pathinfo($evento['flyer'], PATHINFO_EXTENSION); 
                    if(strtolower($ext) == 'pdf'): 
                    ?>
                        <a href="<?= base_url('uploads/eventos/' . $evento['flyer']) ?>" target="_blank" class="btn btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Ver Flyer en PDF
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('uploads/eventos/' . $evento['flyer']) ?>" target="_blank">
                            <img src="<?= base_url('uploads/eventos/' . $evento['flyer']) ?>" class="img-fluid rounded shadow-sm" style="max-height: 400px; width: auto;" alt="Flyer del Evento">
                        </a>
                        <p class="small text-muted mt-2">Clic en la imagen para ampliar</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4"> <!--[cite: 4] -->
        <div class="card shadow-sm border-0 mb-4"> <!--[cite: 4] -->
            <div class="card-header bg-white fw-bold"><i class="bi bi-calendar-check me-2"></i> Logística</div> <!--[cite: 4] -->
            <div class="card-body"> <!--[cite: 4] -->
                <p><i class="bi bi-calendar-event text-primary me-2"></i> <strong>Fecha:</strong><br> <?= date('d/m/Y h:i A', strtotime($evento['fecha_evento'])) ?></p> <!--[cite: 4] -->
                <p><i class="bi bi-geo-alt text-danger me-2"></i> <strong>Ubicación:</strong><br> <?= esc($evento['ubicacion']) ?></p> <!--[cite: 4] -->
                <p><i class="bi bi-cash-coin text-success me-2"></i> <strong>Presupuesto Asignado:</strong><br> $<?= number_format($evento['presupuesto'], 2) ?> MXN</p>
                <hr> <!--[cite: 4] -->
                <p><strong>Lista de Invitados:</strong><br>
                    <span class="badge bg-info text-dark"><?= esc($evento['invitados'] ?: 'No definidos') ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?> <!--[cite: 4] -->