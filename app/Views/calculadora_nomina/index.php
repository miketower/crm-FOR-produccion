<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .card-result { border-top: 4px solid #0d6efd; }
        .text-total { font-size: 1.6rem; font-weight: bold; color: #198754; }
        .bg-custom-blue { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; }
        .form-label { font-weight: 500; font-size: 0.85rem; color: #6c757d; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4"><i class="bi bi-calculator-fill text-primary"></i> Calculadora</h4>
                    
                    <form action="<?= base_url('calculadora') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Tipo de entrada</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="tipo_calculo" id="tipo_diario" value="diario" <?= ($post['tipo_calculo'] == 'diario') ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="tipo_diario">S. Diario</label>

                                    <input type="radio" class="btn-check" name="tipo_calculo" id="tipo_neto" value="neto" <?= ($post['tipo_calculo'] == 'neto') ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="tipo_neto">S. Neto</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="monto" class="form-label" id="label_monto">
                                    <?= ($post['tipo_calculo'] == 'neto') ? 'Sueldo Neto Mensual' : 'Sueldo Base Diario' ?>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto" id="monto" class="form-control" 
                                           value="<?= $post['monto'] ?? '' ?>" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Tipo de Servicio</label>
                                <select name="tipo_servicio" class="form-select form-select-lg">
                                    <option value="especializado" <?= ($post['tipo_servicio'] == 'especializado') ? 'selected' : '' ?>>Especializado</option>
                                    <option value="proem" <?= ($post['tipo_servicio'] == 'proem') ? 'selected' : '' ?>>PROEM</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">% Nómina Propuesta</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" name="pct_nomina" class="form-control" value="<?= $post['pct_nomina'] ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">% Comisión</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" step="0.1" name="pct_comision" class="form-control" value="<?= $post['pct_comision'] ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-2 d-grid">
                                <label class="form-label invisible">Calcular</label>
                                <button type="submit" class="btn btn-dark btn-lg">Calcular</button>
                            </div>
                        </div>

                        <p>
                            <button class="btn btn-link btn-sm p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#paramsLey">
                                <i class="bi bi-sliders"></i> Ajustar Parámetros de Ley y Prestaciones
                            </button>
                        </p>
                        <div class="collapse <?= (isset($resultado)) ? '' : 'show' ?>" id="paramsLey">
                            <div class="card card-body bg-light border-0">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">UMA</label>
                                        <input type="number" step="0.01" name="uma" class="form-control form-control-sm" value="<?= $post['uma'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">S. Mínimo</label>
                                        <input type="number" step="0.01" name="salario_minimo" class="form-control form-control-sm" value="<?= $post['salario_minimo'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">% ISN</label>
                                        <input type="number" step="0.01" name="isn_rate" class="form-control form-control-sm" value="<?= $post['isn_rate'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">% Riesgo Trabajo</label>
                                        <input type="number" step="0.00001" name="prima_riesgo" class="form-control form-control-sm" value="<?= $post['prima_riesgo'] ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Aguinaldo</label>
                                        <input type="number" name="dias_aguinaldo" class="form-control form-control-sm" value="<?= $post['dias_aguinaldo'] ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Vacaciones</label>
                                        <input type="number" name="dias_vacaciones" class="form-control form-control-sm" value="<?= $post['dias_vacaciones'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">% Prima Vac.</label>
                                        <input type="number" name="prima_vacacional_pct" class="form-control form-control-sm" value="<?= $post['prima_vacacional_pct'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($resultado)): ?>
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card card-result shadow-sm h-100">
                        <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary">Recibo Mensual (30 días)</h5></div>
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                    <tr><td>Salario Base Diario</td><td class="text-end">$<?= number_format($resultado['salario_diario'], 2) ?></td></tr>
                                    <tr><td>S. Diario Integrado</td><td class="text-end">$<?= number_format($resultado['sdi'], 2) ?></td></tr>
                                    <tr class="table-light"><td><strong>Sueldo Bruto Mensual</strong></td><td class="text-end"><strong>$<?= number_format($resultado['bruto'], 2) ?></strong></td></tr>
                                    <tr class="text-danger"><td>ISR Retenido</td><td class="text-end">- $<?= number_format($resultado['isr'], 2) ?></td></tr>
                                    <tr class="text-danger"><td>IMSS (Obrero)</td><td class="text-end">- $<?= number_format($resultado['imss_obrero'], 2) ?></td></tr>
                                    <tr class="table-info">
                                        <td><h5 class="mb-0">NETO A RECIBIR</h5></td>
                                        <td class="text-end"><h5 class="mb-0">$<?= number_format($resultado['neto'], 2) ?></h5></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card card-result shadow-sm h-100" style="border-top-color: #198754;">
                        <div class="card-header bg-white py-3"><h5 class="mb-0 text-success">Costo Total Patronal (100% Nómina)</h5></div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="small text-muted"><th>Concepto</th><th class="text-end">Costo Mensual (30.41)</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>Salario Base Diario</td><td class="text-end">$<?= number_format($resultado['salario_diario'] * 30.4166, 2) ?></td></tr>
                                    <tr><td>Provisiones (Aguinaldo/Vac)</td><td class="text-end">$<?= number_format($resultado['provisiones'], 2) ?></td></tr>
                                    <tr><td>IMSS/RCV Patronal</td><td class="text-end">$<?= number_format($resultado['imss_patron'], 2) ?></td></tr>
                                    <tr><td>INFONAVIT (5%)</td><td class="text-end">$<?= number_format($resultado['infonavit'], 2) ?></td></tr>
                                    <tr><td>ISN (<?= $post['isn_rate'] ?>%)</td><td class="text-end">$<?= number_format($resultado['isn'], 2) ?></td></tr>
                                    <tr class="table-success align-middle">
                                        <td class="py-3"><h5 class="mb-0">GASTO REAL TOTAL</h5></td>
                                        <td class="text-end text-total">$<?= number_format($resultado['gasto_real'], 2) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if (isset($propuesta)): ?>
                <div class="col-12">
                    <div class="card shadow border-0 bg-custom-blue">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">
                                <i class="bi bi-shield-check"></i> 
                                Propuesta (<?= ($post['tipo_servicio'] === 'proem') ? 'PROEM' : 'Servicio Especializado' ?>)
                            </h4>
                                <span class="badge bg-white text-primary fs-6">Estrategia <?= $post['pct_nomina'] ?>% Nómina</span>
                            </div>
                            <div class="row g-4 text-center">
                                <div class="col-md-3 border-end border-white-25">
                                    <small class="opacity-75 d-block">Costo Fiscal (Nómina)</small>
                                    <h4 class="fw-bold">$<?= number_format($propuesta['fiscal']['gasto_real'], 2) ?></h4>
                                </div>
                                <div class="col-md-3 border-end border-white-25">
                                    <small class="opacity-75 d-block">Complemento (Libre)</small>
                                    <h4 class="fw-bold">$<?= number_format($propuesta['complemento'], 2) ?></h4>
                                </div>
                                <div class="col-md-3 border-end border-white-25">
                                    <small class="opacity-75 d-block">Comisión (<?= $post['pct_comision'] ?>%)</small>
                                    <h4 class="fw-bold">$<?= number_format($propuesta['comision'], 2) ?></h4>
                                </div>
                                <div class="col-md-3">
                                    <small class="opacity-75 d-block">COSTO TOTAL SERVICIO</small>
                                    <h3 class="fw-bold text-warning">$<?= number_format($propuesta['total_propuesta'], 2) ?></h3>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top border-white-25 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-graph-up-arrow text-warning me-2"></i> 
                                    <strong>Ahorro Estimado para el Patrón:</strong> 
                                    <span class="fs-4 ms-2">$<?= number_format($resultado['gasto_real'] - $propuesta['total_propuesta'], 2) ?> mensuales</span>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-light btn-sm"><i class="bi bi-file-pdf"></i> Generar Cotización</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Cambio dinámico de etiquetas
    document.querySelectorAll('input[name="tipo_calculo"]').forEach((elem) => {
        elem.addEventListener("change", function(event) {
            const label = document.getElementById('label_monto');
            label.innerText = (event.target.value === 'neto') ? 'Sueldo Neto Mensual' : 'Sueldo Base Diario';
        });
    });
</script>
</body>
</html>