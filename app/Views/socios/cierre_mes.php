<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-0">Cierre de Mes - Asociados FOR</h4>
        <small class="text-muted">Registro semanal de resultados comerciales y de asociados</small>
    </div>
    <form action="<?= base_url('socios/cierre-mes') ?>" method="GET" class="d-flex align-items-center gap-2">
        <input type="month" name="mes" class="form-control" value="<?= esc($mesAnio) ?>" onchange="this.form.submit()">
    </form>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('msg') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

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
                        <tr><td colspan="7" class="text-center text-muted py-3">Sin registros este mes.</td></tr>
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

<!-- CAPTURA DE SEGUIMIENTO -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-bold py-3">
        <i class="bi bi-plus-circle-dotted me-2 text-primary"></i>Registrar Seguimiento Semanal
    </div>
    <div class="card-body">
    <form action="<?= base_url('socios/guardar-seguimiento') ?>" method="POST">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Semana / Fecha</label>
            <input type="date" name="semana_fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Asociado FOR</label>
            <!-- SE AGREGÓ EL ID: select-socio -->
            <select name="socio_id" id="select-socio" class="form-select" required>
                <option value="">-- Buscar asociado... --</option>
                <?php foreach($socios as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= esc($s['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Cliente / Prospecto</label>
            <!-- SE AGREGÓ EL ID: select-prospecto -->
            <select name="prospect_id" id="select-prospecto" class="form-select">
                <option value="">-- Buscar cliente... --</option>
                <?php foreach($prospects as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= esc($p['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Citas Realizadas</label>
            <input type="number" name="citas_realizadas" class="form-control" value="0" min="0">
        </div>
    </div>

            <div class="row g-3 mt-1">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Citas Programadas</label>
                    <input type="number" name="citas_programadas" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Propuestas</label>
                    <input type="number" name="propuestas" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Cierres</label>
                    <input type="number" name="cierres" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Asociados Nuevos</label>
                    <input type="number" name="asociados_nuevos" class="form-control" value="0" min="0">
                </div>
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="bi bi-save me-1"></i> Agregar Seguimiento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const ctx = document.getElementById('graficaAsociados').getContext('2d');
    const datos = <?= json_encode($resumenPorAsociado) ?>;

    new Chart(ctx, {
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

    // --- NUEVO: Inicializar Autocomplete en los Selects ---
    new TomSelect("#select-socio", {
        create: false,
        placeholder: "-- Buscar asociado... --",
    });

    new TomSelect("#select-prospecto", {
        create: false,
        placeholder: "-- Buscar cliente... --",
    });
</script>

<?= $this->endSection() ?>