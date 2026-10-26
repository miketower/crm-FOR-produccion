<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Resumen de Cotización #<?= esc($propuesta['id']) ?></h2>
        <p class="text-muted small">Generada el <?= date('d/m/Y', strtotime($propuesta['fecha_creacion'])) ?></p>
    </div>
    <div>
        <a href="<?= base_url('cotizador') ?>" class="btn btn-outline-secondary shadow-sm fw-bold me-2">
            <i class="bi bi-arrow-left me-1"></i> Volver al Listado
        </a>
        <!-- Aquí luego puedes reconectar la función para regenerar el PDF -->
        <button class="btn btn-primary shadow-sm fw-bold">
            <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Columna Izquierda: Datos Generales -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-white h-100">
            <div class="card-body">
                <h5 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-2"></i>Datos Generales</h5>
                
                <p class="mb-1 small text-muted fw-bold">Cliente</p>
                <p class="fw-bold text-dark mb-3"><?= esc($propuesta['nombre_cliente']) ?></p>

                <p class="mb-1 small text-muted fw-bold">Solicitante</p>
                <p class="mb-3"><?= esc($propuesta['solicitante']) ?> <br><span class="small text-muted"><?= esc($propuesta['cargo_solicitante']) ?></span></p>

                <p class="mb-1 small text-muted fw-bold">Plan Requerido</p>
                <p class="mb-3"><span class="badge bg-primary"><?= esc($propuesta['plan_requerido']) ?></span></p>

                <p class="mb-1 small text-muted fw-bold">Nivel de Riesgo</p>
                <?php 
                    $riesgoColors = ['0' => 'success', '1' => 'warning', '2' => 'orange', '3' => 'danger'];
                    $color = $riesgoColors[$propuesta['nivel_riesgo']] ?? 'secondary';
                ?>
                <p class="mb-3"><span class="badge bg-<?= $color ?> text-white">Nivel <?= esc($propuesta['nivel_riesgo']) ?></span></p>

                <p class="mb-1 small text-muted fw-bold">Comisión Acordada</p>
                <p class="mb-0 fw-bold fs-5 text-dark"><?= esc($propuesta['comision_pct']) ?>%</p>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Detalle Financiero -->
    <div class="col-md-8">
        <!-- Escenario Actual -->
        <div class="card shadow-sm border-0 bg-light border-start border-warning border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">Escenario Actual (Baseline)</h6>
                <div class="row text-center g-2">
                    <div class="col"><p class="small text-muted mb-0">Bruto</p><p class="fw-bold">$<?= number_format($propuesta['actual_bruto'], 2) ?></p></div>
                    <div class="col"><p class="small text-muted mb-0">Carga</p><p class="fw-bold">$<?= number_format($propuesta['actual_carga'], 2) ?></p></div>
                    <div class="col"><p class="small text-muted mb-0">Excedente</p><p class="fw-bold">$<?= number_format($propuesta['actual_excedente'], 2) ?></p></div>
                    <div class="col"><p class="small text-muted mb-0">Comisión</p><p class="fw-bold">$<?= number_format($propuesta['actual_comision'], 2) ?></p></div>
                    <div class="col border-start border-warning"><p class="small text-warning fw-bold mb-0">Total Actual</p><p class="fw-bold fs-5">$<?= number_format($propuesta['actual_total'], 2) ?></p></div>
                </div>
            </div>
        </div>

        <!-- Escenario Propuesto (Dependiendo del Plan) -->
        <?php if ($proem): ?>
        <div class="card shadow-sm border-0 bg-white border-start border-primary border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3">Escenario PROEM</h6>
                <div class="row text-center g-2 mb-3">
                    <div class="col"><p class="small text-muted mb-0">Costo 100% IMSS</p><p class="fw-bold text-dark">$<?= number_format($proem['costo_op_100'], 2) ?></p></div>
                    <div class="col"><p class="small text-muted mb-0">Carga PROEM</p><p class="fw-bold">$<?= number_format($proem['carga_proem'], 2) ?></p></div>
                    <div class="col"><p class="small text-muted mb-0">Comisión Monto</p><p class="fw-bold text-primary">$<?= number_format($proem['comision_monto'], 2) ?></p></div>
                    <div class="col border-start border-primary"><p class="small text-primary fw-bold mb-0">Total PROEM</p><p class="fw-bold fs-5 text-primary">$<?= number_format($proem['total_proem'], 2) ?></p></div>
                </div>
                <div class="p-2 bg-success bg-opacity-10 rounded text-center border border-success text-success fw-bold">
                    Ahorro Mensual Estimado: $<?= number_format($proem['ahorro_mensual'], 2) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($esp): ?>
        <div class="card shadow-sm border-0 bg-white border-start border-info border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-info mb-3">Escenario Especializado</h6>
                <div class="row text-center g-2 mb-3">
                    <div class="col"><p class="small text-muted mb-0">Costo 100% IMSS</p><p class="fw-bold text-dark">$<?= number_format($esp['costo_op_100'], 2) ?></p></div>
                    <div class="col"><p class="small text-muted mb-0">Carga FOR</p><p class="fw-bold">$<?= number_format($esp['carga_for'], 2) ?></p></div>
                    <div class="col"><p class="small text-muted mb-0">Comisión Monto</p><p class="fw-bold text-info">$<?= number_format($esp['comision_monto'], 2) ?></p></div>
                    <div class="col border-start border-info"><p class="small text-info fw-bold mb-0">Total FOR</p><p class="fw-bold fs-5 text-info">$<?= number_format($esp['total_for'], 2) ?></p></div>
                </div>
                <div class="p-2 bg-dark rounded text-center text-white fw-bold">
                    Diferencia / Ahorro Mensual: $<?= number_format($esp['diferencia'], 2) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tablas Inferiores -->
<div class="row g-4 mt-1">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 bg-white h-100">
            <div class="card-body p-0">
                <h6 class="fw-bold text-dark p-3 border-bottom mb-0"><i class="bi bi-people me-2"></i>Perfiles Cotizados</h6>
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light text-muted small">
                        <tr><th class="ps-3">Cant.</th><th>Cargo / Puesto</th><th>Esquema</th></tr>
                    </thead>
                    <tbody>
                        <?php if($perfiles): ?>
                            <?php foreach($perfiles as $perf): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?= esc($perf['cantidad']) ?></td>
                                    <td><?= esc($perf['cargo']) ?></td>
                                    <td><span class="badge bg-secondary"><?= strtoupper(esc($perf['tipo_plan'])) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">No hay perfiles registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 bg-white h-100">
            <div class="card-body p-0">
                <h6 class="fw-bold text-dark p-3 border-bottom mb-0"><i class="bi bi-bar-chart me-2"></i>Market Benchmark</h6>
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light text-muted small">
                        <tr><th class="ps-3">Puesto</th><th class="text-center">Mediana</th><th class="text-center">75° Perc.</th></tr>
                    </thead>
                    <tbody>
                        <?php if($benchmarks): ?>
                            <?php foreach($benchmarks as $bench): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?= esc($bench['puesto']) ?></td>
                                    <td class="text-center text-muted">$<?= number_format($bench['median'], 2) ?></td>
                                    <td class="text-center text-success">$<?= number_format($bench['percentile_75'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">No se registró benchmark.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>