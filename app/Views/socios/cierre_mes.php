<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
$cierreUrl = base_url('socios/cierre-mes');
$queryRango = http_build_query(['desde' => $fechaInicio, 'hasta' => $fechaFin]);
$cierreRangoUrl = $cierreUrl . '?' . $queryRango;
$indicadores = [
    'citas_realizadas' => 'Citas realizadas',
    'citas_programadas' => 'Citas programadas',
    'propuestas' => 'Propuestas',
    'cierres' => 'Cierres',
    'asociados_nuevos' => 'Nuevos asociados',
];
$valor = static function (string $campo, $predeterminado = '') use ($seguimiento) {
    $valor = old($campo, $seguimiento[$campo] ?? $predeterminado, false);
    return is_scalar($valor) ? (string) $valor : '';
};
?>

<h4 class="fw-bold text-dark mb-3">Cierre de Mes - Asociados FOR</h4>
<form action="<?= esc($cierreUrl, 'attr') ?>" method="GET" class="card border-0 shadow-sm p-3 mb-4">
    <p class="fw-semibold mb-2">Período del resumen</p>
    <div class="d-flex gap-2 flex-wrap mb-3">
        <?php foreach ($rangos as $etiqueta => [$inicio, $fin]): ?>
            <?php $seleccionado = $inicio === $fechaInicio && $fin === $fechaFin; ?>
            <a class="btn btn-sm <?= $seleccionado ? 'btn-primary' : 'btn-outline-primary' ?>"
               <?= $seleccionado ? 'aria-current="true"' : '' ?>
               href="<?= esc($cierreUrl . '?' . http_build_query(['desde' => $inicio, 'hasta' => $fin]), 'attr') ?>"><?= esc($etiqueta) ?></a>
        <?php endforeach; ?>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-end">
        <div>
            <label for="rango-desde" class="form-label">Desde</label>
            <input type="date" id="rango-desde" name="desde" class="form-control" value="<?= esc($fechaInicio, 'attr') ?>" required>
        </div>
        <div>
            <label for="rango-hasta" class="form-label">Hasta</label>
            <input type="date" id="rango-hasta" name="hasta" class="form-control" value="<?= esc($fechaFin, 'attr') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Aplicar rango</button>
    </div>
    <?php if ($errorRango): ?>
        <div class="alert alert-warning mt-3 mb-0" role="alert"><?= esc($errorRango) ?></div>
    <?php endif; ?>
</form>

<!-- TARJETAS DE KPIs -->
<div class="row g-3 mb-4">
    <div class="col">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-primary border-4">
            <span class="text-muted small fw-bold">CITAS REALIZADAS</span>
            <h3 class="fw-bold text-primary my-1"><?= $kpis['total_citas_realizadas'] ?? 0 ?></h3>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-info border-4">
            <span class="text-muted small fw-bold">CITAS PROGRAMADAS</span>
            <h3 class="fw-bold text-info my-1"><?= $kpis['total_citas_programadas'] ?? 0 ?></h3>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-warning border-4">
            <span class="text-muted small fw-bold">PROPUESTAS</span>
            <h3 class="fw-bold text-warning my-1"><?= $kpis['total_propuestas'] ?? 0 ?></h3>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-success border-4">
            <span class="text-muted small fw-bold">CIERRES</span>
            <h3 class="fw-bold text-success my-1"><?= $kpis['total_cierres'] ?? 0 ?></h3>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-secondary border-4">
            <span class="text-muted small fw-bold">NUEVOS ASOCIADOS</span>
            <h3 class="fw-bold text-secondary my-1"><?= $kpis['total_nuevos_asociados'] ?? 0 ?></h3>
        </div>
    </div>
</div>

<!-- RESUMEN POR ASOCIADO Y GRÁFICA -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-bold py-3">
        <i class="bi bi-bar-chart-line text-primary me-2"></i>Resumen del Seguimiento por Asociado
    </div>
    <div class="card-body">
        <div class="table-responsive mb-4">
            <table class="table table-hover align-middle">
                <thead class="bg-light small">
                    <tr>
                        <th>ASOCIADO</th>
                        <th>MODALIDAD</th>
                        <th class="text-center">CITAS REALIZ.</th>
                        <th class="text-center">CITAS PROGR.</th>
                        <th class="text-center">PROPUESTAS</th>
                        <th class="text-center">CIERRES</th>
                        <th class="text-center">ASOC. NVO.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($resumenPorAsociado)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-3">Sin registros en este período.</td></tr>
                    <?php else: ?>
                        <?php foreach($resumenPorAsociado as $r): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($r['socio_nombre']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($r['tipo_participacion'] ?? 'FOR') ?></span></td>
                                <td class="text-center fw-bold text-primary"><?= $r['citas_realiz'] ?></td>
                                <td class="text-center"><?= $r['citas_progr'] ?></td>
                                <td class="text-center text-warning fw-bold"><?= $r['propuestas'] ?></td>
                                <td class="text-center text-success fw-bold"><?= $r['cierres'] ?></td>
                                <td class="text-center"><?= $r['asoc_nvo'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- GRÁFICA COMPARATIVA -->
        <canvas id="graficaAsociados" height="80"></canvas>
    </div>
</div>

<?php if ($puedeEditar): ?>
    <?php $accion = $seguimiento ? 'socios/actualizar-seguimiento/' . $seguimiento['id'] : 'socios/guardar-seguimiento'; ?>
    <section id="form-seguimiento" class="card border-0 shadow-sm mb-4" aria-labelledby="titulo-form-seguimiento">
        <h5 id="titulo-form-seguimiento" class="card-header bg-white fw-bold py-3 mb-0">
            <?= $seguimiento ? 'Editar seguimiento semanal' : 'Registrar Seguimiento Semanal' ?>
        </h5>
        <div class="card-body">
            <?php if ($error = session()->getFlashdata('error_seguimiento')): ?>
                <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
            <?php endif; ?>
            <form action="<?= esc(base_url($accion) . '?' . $queryRango, 'attr') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="seguimiento-fecha" class="form-label">Fecha de seguimiento</label>
                        <input type="date" id="seguimiento-fecha" name="semana_fecha" class="form-control"
                               value="<?= esc($valor('semana_fecha', date('Y-m-d')), 'attr') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="select-socio" class="form-label">Asociado FOR</label>
                        <select name="socio_id" id="select-socio" class="form-select" required>
                            <option value="">Buscar asociado</option>
                            <?php foreach ($socios as $socio): ?>
                                <option value="<?= (int) $socio['id'] ?>" <?= (string) $socio['id'] === $valor('socio_id') ? 'selected' : '' ?>>
                                    <?= esc($socio['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="select-prospecto" class="form-label">Cliente / Prospecto</label>
                        <select name="prospect_id" id="select-prospecto" class="form-select">
                            <option value="">Sin prospecto</option>
                            <?php foreach ($prospects as $prospecto): ?>
                                <option value="<?= (int) $prospecto['id'] ?>" <?= (string) $prospecto['id'] === $valor('prospect_id') ? 'selected' : '' ?>>
                                    <?= esc($prospecto['company_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php foreach ($indicadores as $campo => $etiqueta): ?>
                        <div class="col-6 col-md">
                            <label for="seguimiento-<?= $campo ?>" class="form-label"><?= esc($etiqueta) ?></label>
                            <input type="number" id="seguimiento-<?= $campo ?>" name="<?= $campo ?>" class="form-control"
                                   value="<?= esc($valor($campo, 0), 'attr') ?>" min="0" max="2147483647">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 text-end">
                    <?php if ($seguimiento): ?>
                        <a href="<?= esc($cierreRangoUrl . '#bitacora-seguimiento', 'attr') ?>" class="btn btn-outline-secondary">Cancelar</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">
                        <?= $seguimiento ? 'Guardar cambios' : 'Agregar seguimiento' ?>
                    </button>
                </div>
            </form>
        </div>
    </section>
<?php endif; ?>

<section id="bitacora-seguimiento" class="card border-0 shadow-sm mb-4" aria-labelledby="titulo-bitacora">
    <?php if ($mensaje = session()->getFlashdata('msg')): ?>
        <div class="alert alert-success m-3" role="status"><?= esc($mensaje) ?></div>
    <?php endif; ?>
    <div class="card-header bg-white py-3">
        <h5 id="titulo-bitacora" class="fw-bold mb-1">Bitácora de seguimientos</h5>
        <p class="small text-muted mb-0">Historial completo de capturas, de la más reciente a la más antigua por fecha de seguimiento.</p>
    </div>
    <?php if (!$bitacora): ?>
        <p class="text-muted text-center p-3 mb-0">Aún no hay seguimientos registrados.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Fecha</th>
                        <th scope="col">Asociado</th>
                        <th scope="col">Cliente / Prospecto</th>
                        <?php foreach ($indicadores as $etiqueta): ?>
                            <th scope="col" class="text-end"><?= esc($etiqueta) ?></th>
                        <?php endforeach; ?>
                        <?php if ($puedeEditar): ?><th scope="col">Acción</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bitacora as $registro): ?>
                        <tr>
                            <td class="text-nowrap"><?= esc($registro['semana_fecha']) ?></td>
                            <td><?= esc($registro['socio_nombre'] ?? '—') ?></td>
                            <td><?= esc($registro['cliente_nombre'] ?? '—') ?></td>
                            <?php foreach ($indicadores as $campo => $etiqueta): ?>
                                <td class="text-end"><?= (int) $registro[$campo] ?></td>
                            <?php endforeach; ?>
                            <?php if ($puedeEditar): ?>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="<?= esc($cierreRangoUrl . '&editar=' . (int) $registro['id'] . '#form-seguimiento', 'attr') ?>">Editar</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
    const ctx = document.getElementById('graficaAsociados').getContext('2d');
    const datos = <?= json_encode($resumenPorAsociado, JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE) ?>;

    if (typeof Chart !== "undefined") new Chart(ctx, {
        type: 'bar',
        data: {
            labels: datos.map(d => d.socio_nombre),
            datasets: [
                { label: 'Citas Realizadas', data: datos.map(d => d.citas_realiz), backgroundColor: '#0d6efd' },
                { label: 'Propuestas', data: datos.map(d => d.propuestas), backgroundColor: '#ffc107' },
                { label: 'Cierres', data: datos.map(d => d.cierres), backgroundColor: '#198754' }
            ]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    if (typeof TomSelect !== "undefined" && document.getElementById("select-socio")) {
        new TomSelect("#select-socio", { create: false });
        new TomSelect("#select-prospecto", { create: false });
    }
</script>

<?= $this->endSection() ?>