<?= $this->extend('layout/main'); ?>

<?= $this->section('contenido'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cuestionario de Alineación Estratégica</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('formularios/guardarAlineacion') ?>" method="POST">
                        <?= csrf_field() ?>

                        <!-- I. DATOS GENERALES -->
                        <h4 class="text-primary border-bottom pb-2">I. DATOS GENERALES</h4>
                        <div class="row pt-3">
                            <div class="col-md-6 mb-3">
                                <label>Empresa:</label>
                                <input type="text" name="empresa" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Fecha:</label>
                                <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Estado / País:</label>
                                <input type="text" name="estado_pais" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nombre Completo:</label>
                                <input type="text" name="nombre_completo" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Puesto:</label>
                                <input type="text" name="puesto" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Área:</label>
                                <input type="text" name="area" class="form-control">
                            </div>
                        </div>

                        <!-- II. EXPECTATIVAS Y VISIÓN -->
                        <h4 class="text-primary border-bottom pb-2 mt-4">II. EXPECTATIVAS Y VISIÓN</h4>
                        <div class="mb-3">
                            <label>1. ¿Qué esperas personalmente de este proyecto?</label>
                            <textarea name="expectativa_personal" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>2. ¿Cómo visualizas a la empresa en los próximos 12 meses?</label>
                            <textarea name="vision_12_meses" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- III. ÁREAS CRÍTICAS (Checkbox Múltiple) -->
                        <div class="mb-4">
                            <label class="d-block">3. Áreas que consideras críticas actualmente:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="areas_criticas[]" value="Operaciones">
                                <label class="form-check-label">Operaciones</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="areas_criticas[]" value="RH">
                                <label class="form-check-label">RH</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="areas_criticas[]" value="Comercial">
                                <label class="form-check-label">Comercial</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="areas_criticas[]" value="Compras">
                                <label class="form-check-label">Compras</label>
                            </div>
                        </div>

                        <!-- IV. PRIORIDADES (Rankings Numéricos) -->
                        <h4 class="text-primary border-bottom pb-2 mt-4">IV. PRIORIDADES DE ORDEN (1 al 5)</h4>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Operaciones:</label>
                                <input type="number" name="orden_operaciones" class="form-control" min="1" max="5">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Compras:</label>
                                <input type="number" name="orden_compras" class="form-control" min="1" max="5">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Marketing:</label>
                                <input type="number" name="orden_marketing" class="form-control" min="1" max="5">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Recursos Humanos:</label>
                                <input type="number" name="orden_rh" class="form-control" min="1" max="5">
                            </div>
                        </div>

                        <!-- V. DESEMPEÑO (Escala 1-10) -->
                        <h4 class="text-primary border-bottom pb-2 mt-4">V. AUTODIAGNÓSTICO DE DESEMPEÑO (1 al 10)</h4>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Claridad de Objetivos:</label>
                                <select name="perf_claridad_objetivos" class="form-control">
                                    <?php for($i=1;$i<=10;$i++): ?> <option value="<?=$i?>"><?=$i?></option> <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Accountability:</label>
                                <select name="perf_accountability" class="form-control">
                                    <?php for($i=1;$i<=10;$i++): ?> <option value="<?=$i?>"><?=$i?></option> <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Calidad del Talento:</label>
                                <select name="perf_calidad_talento" class="form-control">
                                    <?php for($i=1;$i<=10;$i++): ?> <option value="<?=$i?>"><?=$i?></option> <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <div class="text-right">
                            <button type="reset" class="btn btn-secondary">Limpiar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cuestionario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>