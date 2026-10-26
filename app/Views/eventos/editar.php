<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="mb-4">
    <a href="<?= base_url('eventos/show/' . $evento['id']) ?>" class="text-decoration-none text-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Regresar al detalle</a>
    <h4 class="fw-bold text-dark mb-0">Editar Evento</h4>
</div>

<div class="card shadow border-0">
    <div class="card-body p-4">
        <form action="<?= base_url('eventos/actualizar/' . $evento['id']) ?>" method="POST">
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <label class="fw-bold text-primary mb-2">Categoría del Evento</label>
                    <select name="categoria_evento" id="categoria_evento" class="form-select" onchange="toggleCamposEvento()" required>
                        <option value="Externo" <?= $evento['categoria_evento'] == 'Externo' ? 'selected' : '' ?>>Evento Externo (Networking, Expos, etc.)</option>
                        <option value="Interno" <?= $evento['categoria_evento'] == 'Interno' ? 'selected' : '' ?>>Evento Interno (Capacitación, Integración, etc.)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-primary mb-2">Estatus</label>
                    <select name="estatus" class="form-select fw-bold">
                        <option value="Planeación" <?= $evento['estatus'] == 'Planeación' ? 'selected' : '' ?>>Planeación</option>
                        <option value="Pendiente" <?= $evento['estatus'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="Realizado" <?= $evento['estatus'] == 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                        <option value="Cancelado" <?= $evento['estatus'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
            </div>

            <!-- DATOS GENERALES (COMPARTIDOS) -->
            <h6 class="fw-bold text-muted border-bottom pb-2">Datos Generales</h6>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Nombre del Evento</label>
                    <input type="text" name="titulo" class="form-control" value="<?= esc($evento['titulo']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Fecha y Hora</label>
                    <?php 
                        // Formatear fecha para el input datetime-local
                        $fechaEdit = date('Y-m-d\TH:i', strtotime($evento['fecha_evento']));
                    ?>
                    <input type="datetime-local" name="fecha_evento" class="form-control" value="<?= $fechaEdit ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control" value="<?= esc($evento['ubicacion']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Liga de Registro / Videollamada</label>
                    <input type="url" name="liga_registro" class="form-control" value="<?= esc($evento['liga_registro']) ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Objetivo del Evento</label>
                    <textarea name="objetivo" class="form-control" rows="2"><?= esc($evento['objetivo']) ?></textarea>
                </div>
            </div>

            <!-- CAMPOS EXCLUSIVOS: EVENTO EXTERNO -->
            <div id="seccion_externo">
                <h6 class="fw-bold text-muted border-bottom pb-2 mt-2">Detalles del Evento Externo</h6>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Quién organiza</label>
                        <input type="text" name="organizador" class="form-control" value="<?= esc($evento['organizador'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold">Costo (MXN)</label>
                        <input type="number" step="0.01" name="costo" class="form-control" value="<?= esc($evento['costo'] ?? 0) ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold">Alcance</label>
                        <select name="alcance" class="form-select">
                            <option value="Local" <?= ($evento['alcance'] ?? '') == 'Local' ? 'selected' : '' ?>>Local</option>
                            <option value="Nacional" <?= ($evento['alcance'] ?? '') == 'Nacional' ? 'selected' : '' ?>>Nacional</option>
                            <option value="Internacional" <?= ($evento['alcance'] ?? '') == 'Internacional' ? 'selected' : '' ?>>Internacional</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Industria / Audiencia / Segmentación</label>
                        <textarea name="industria_segmentacion" class="form-control" rows="2"><?= esc($evento['industria_segmentacion'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold d-block mb-2">¿Quién debe asistir de parte de FOR?</label>
                    <?php $rolesGuardados = explode(', ', $evento['roles_asistencia'] ?? ''); ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="roles_asistencia[]" value="Especialistas" <?= in_array('Especialistas', $rolesGuardados) ? 'checked' : '' ?>>
                        <label class="form-check-label">Especialistas</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="roles_asistencia[]" value="Relaciones Públicas" <?= in_array('Relaciones Públicas', $rolesGuardados) ? 'checked' : '' ?>>
                        <label class="form-check-label">Relaciones Públicas</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="roles_asistencia[]" value="CEO" <?= in_array('CEO', $rolesGuardados) ? 'checked' : '' ?>>
                        <label class="form-check-label">CEO</label>
                    </div>
                </div>
            </div>

            <!-- CAMPOS EXCLUSIVOS: EVENTO INTERNO -->
            <div id="seccion_interno" class="d-none">
                <h6 class="fw-bold text-muted border-bottom pb-2 mt-2">Detalles del Evento Interno</h6>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Responsable del Evento</label>
                        <input type="text" name="responsable" class="form-control" value="<?= esc($evento['responsable'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Presupuesto Asignado (MXN)</label>
                        <input type="number" step="0.01" name="presupuesto" class="form-control" value="<?= esc($evento['presupuesto'] ?? 0) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Tipo de Evento</label>
                        <input type="text" name="tipo_evento" class="form-control" value="<?= esc($evento['tipo_evento'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Invitados</label>
                        <input type="text" name="invitados" class="form-control" value="<?= esc($evento['invitados'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Capacidad / Aforo</label>
                        <input type="number" name="capacidad" class="form-control" value="<?= esc($evento['capacidad'] ?? 0) ?>">
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <a href="<?= base_url('eventos/show/' . $evento['id']) ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary fw-bold">Actualizar Evento</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleCamposEvento() {
        var tipo = document.getElementById('categoria_evento').value;
        var secExterno = document.getElementById('seccion_externo');
        var secInterno = document.getElementById('seccion_interno');

        if (tipo === 'Externo') {
            secExterno.classList.remove('d-none');
            secInterno.classList.add('d-none');
        } else {
            secExterno.classList.add('d-none');
            secInterno.classList.remove('d-none');
        }
    }

    // Ejecutar la función al cargar la página para que muestre los campos correctos
    document.addEventListener("DOMContentLoaded", function() {
        toggleCamposEvento();
    });
</script>

<?= $this->endSection() ?>