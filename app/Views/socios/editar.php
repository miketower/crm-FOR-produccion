<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<form action="<?= base_url('socios/actualizar/' . $socio['id']) ?>" method="POST">
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Editar Asociado FOR: <?= esc($socio['nombre']) ?></h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= esc($socio['nombre']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Celular</label>
                    <input type="text" name="celular" class="form-control" value="<?= esc($socio['celular']) ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Semáforo</label>
                    <select name="cat_semaforo_id" class="form-select">
                        <option value="">-- Seleccionar Semáforo --</option>
                        <?php foreach($semaforos as $sem): ?>
                            <option value="<?= $sem['id'] ?>" <?= ($socio['cat_semaforo_id'] == $sem['id']) ? 'selected' : '' ?>><?= esc($sem['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Invitado por (Red)</label>
                    <select name="red_id" class="form-select">
                        <option value="">-- Nadie (Líder / Directo) --</option>
                        <?php foreach($posibles_redes as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($socio['red_id'] == $r['id']) ? 'selected' : '' ?>>
                                <?= esc($r['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Fecha de Ingreso</label>
                    <input type="date" name="fecha_entrada" class="form-control" value="<?= esc($socio['fecha_entrada'] ?? '') ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Servicios que comisiona</label>
                    <select name="servicios_comisiona" class="form-select">
                        <option value="No aplica" <?= ($socio['servicios_comisiona'] == 'No aplica') ? 'selected' : '' ?>>No aplica</option>
                        <option value="Optimización" <?= ($socio['servicios_comisiona'] == 'Optimización') ? 'selected' : '' ?>>Optimización</option>
                        <option value="Soluciones" <?= ($socio['servicios_comisiona'] == 'Soluciones') ? 'selected' : '' ?>>Soluciones</option>
                        <option value="Flujos" <?= ($socio['servicios_comisiona'] == 'Flujos') ? 'selected' : '' ?>>Flujos</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Tipo de Participación</label>
                    <select name="tipo_participacion" class="form-select">
                        <option value="Asociado FOR" <?= ($socio['tipo_participacion'] == 'Asociado FOR') ? 'selected' : '' ?>>Asociado FOR</option>
                        <option value="Unidad Estratégica" <?= ($socio['tipo_participacion'] == 'Unidad Estratégica') ? 'selected' : '' ?>>Unidad Estratégica</option>
                        <option value="Prosperhaus" <?= ($socio['tipo_participacion'] == 'Prosperhaus') ? 'selected' : '' ?>>Prosperhaus</option>
                        <option value="Socios Patrimoniales" <?= ($socio['tipo_participacion'] == 'Socios Patrimoniales') ? 'selected' : '' ?>>Socios Patrimoniales</option>
                        <option value="Comunidad" <?= ($socio['tipo_participacion'] == 'Comunidad') ? 'selected' : '' ?>>Comunidad</option>
                        <option value="Socio Director" <?= ($socio['tipo_participacion'] == 'Socio Director') ? 'selected' : '' ?>>Socio Director</option>
                        <option value="FOR" <?= ($socio['tipo_participacion'] == 'FOR') ? 'selected' : '' ?>>FOR</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="fw-bold">Nivel de Participación</label>
                    <select name="nivel_participacion" class="form-select">
                        <option value="Activo" <?= (($socio['nivel_participacion'] ?? '') == 'Activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="Interesada" <?= (($socio['nivel_participacion'] ?? '') == 'Interesada') ? 'selected' : '' ?>>Interesada</option>
                        <option value="Comisiona" <?= (($socio['nivel_participacion'] ?? '') == 'Comisiona') ? 'selected' : '' ?>>Comisiona</option>
                        <option value="Inactivo" <?= (($socio['nivel_participacion'] ?? '') == 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
                        <option value="Baja" <?= (($socio['nivel_participacion'] ?? '') == 'Baja') ? 'selected' : '' ?>>Baja</option>
                    </select>
                </div>
            </div>
            <hr class="my-4">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-text me-2"></i>Gestión de Convenio</h6>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Estatus Convenio</label>
                    <select name="estatus_convenio" class="form-select">
                        <option value="Pendiente" <?= ($socio['estatus_convenio'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="Realizado" <?= ($socio['estatus_convenio'] == 'Realizado') ? 'selected' : '' ?>>Realizado</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Fecha de Firma</label>
                    <input type="date" name="fecha_firma" class="form-control" value="<?= esc($socio['fecha_firma'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Responsable Resguardo</label>
                    <input type="text" name="responsable_resguardo" class="form-control" value="<?= esc($socio['responsable_resguardo'] ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Ubicación Física</label>
                    <input type="text" name="ubicacion_fisica" class="form-control" value="<?= esc($socio['ubicacion_fisica'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Ubicación Digital</label>
                    <input type="text" name="ubicacion_digital" class="form-control" value="<?= esc($socio['ubicacion_digital'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Notas</label>
                <textarea name="notas" class="form-control" rows="3"><?= esc($socio['notas'] ?? '') ?></textarea>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="<?= base_url('socios/show/' . $socio['id']) ?>" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>