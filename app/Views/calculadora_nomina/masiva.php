<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<style>
    .bg-victoria { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: white; }
    .card-result { border-top: 4px solid #0d6efd; }
    .text-total { font-size: 1.4rem; font-weight: bold; color: #198754; }
    .form-label { font-weight: 500; font-size: 0.85rem; color: #6c757d; }
    .accordion-button:not(.collapsed) { background-color: #f8fafc; color: #1e40af; }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i> Calculadora de Proyectos Masivos</h4>
                    
                    <form action="<?= base_url('calculadora') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle" id="tabla-proyectos">
                                <thead class="table-light">
                                    <tr class="small text-muted text-uppercase">
                                        <th style="width: 7.5%;">Cant.</th>
                                        <th style="width: 15%;">Sueldo Neto</th>
                                        <th style="width: 15%;">Bruto (Opc.)</th>
                                        <th style="width: 15%;">SBC (Opc.)</th>
                                        <th style="width: 10%;">Tipo</th>
                                        <th style="width: 10%;">Servicio</th>
                                        <th style="width: 15%;">Nómina</th>
                                        <th style="width: 7.5%;">% Com.</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($post['filas'] as $idx => $f): ?>
                                    <tr>
                                        <td>
                                            <input type="number" name="filas[<?= $idx ?>][cantidad]" class="form-control" value="<?= $f['cantidad'] ?>" min="1">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="filas[<?= $idx ?>][monto]" class="form-control" value="<?= $f['monto'] ?>" placeholder="0.00" required>
                                        </td>
                                        
                                        <!-- CAMPO: Sueldo Bruto Manual -->
                                        <td>
                                            <input type="number" step="0.01" name="filas[<?= $idx ?>][sueldo_bruto_manual]" class="form-control" value="<?= $f['sueldo_bruto_manual'] ?? '' ?>" placeholder="Ej. 30453.63">
                                        </td>
                                        
                                        <!-- CAMPO: SBC Manual -->
                                        <td>
                                            <input type="number" step="0.01" name="filas[<?= $idx ?>][sbc_manual]" class="form-control" value="<?= $f['sbc_manual'] ?? '' ?>" placeholder="Ej. 1051.15">
                                        </td>

                                        <td>
                                            <select name="filas[<?= $idx ?>][tipo_calculo]" class="form-select">
                                                <option value="diario" <?= $f['tipo_calculo'] == 'diario' ? 'selected' : '' ?>>Diario</option>
                                                <option value="neto" <?= $f['tipo_calculo'] == 'neto' ? 'selected' : '' ?>>Mensual</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="filas[<?= $idx ?>][tipo_servicio]" class="form-select">
                                                <option value="especializado" <?= $f['tipo_servicio'] == 'especializado' ? 'selected' : '' ?>>Especializado</option>
                                                <option value="proem" <?= $f['tipo_servicio'] == 'proem' ? 'selected' : '' ?>>PROEM</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <select name="filas[<?= $idx ?>][modo_nomina]" class="form-select bg-light" style="max-width: 65px; font-size: 0.7rem;">
                                                    <option value="pct" <?= (isset($f['modo_nomina']) && $f['modo_nomina'] == 'pct') ? 'selected' : '' ?>>%</option>
                                                    <option value="sdi" <?= (isset($f['modo_nomina']) && $f['modo_nomina'] == 'sdi') ? 'selected' : '' ?>>SDI</option>
                                                </select>
                                                <input type="number" step="0.01" name="filas[<?= $idx ?>][valor_nomina]" class="form-control" value="<?= $f['valor_nomina'] ?? 40 ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" step="0.1" name="filas[<?= $idx ?>][pct_comision]" class="form-control" value="<?= $f['pct_comision'] ?>">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold shadow-sm" id="add-row">
                                <i class="bi bi-plus-circle"></i> Agregar Grupo de Sueldo
                            </button>
                            
                            <button class="btn btn-link btn-sm text-decoration-none text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#paramsLey">
                                <i class="bi bi-sliders"></i> Parámetros de Ley
                            </button>
                        </div>

                        <div class="collapse mb-4" id="paramsLey">
                            <div class="card card-body bg-light border-0 shadow-sm">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Factor Integración</label>
                                        <input type="number" step="0.0001" name="config[factor_integracion]" class="form-control form-control-sm" value="<?= $post['config']['factor_integracion'] ?? 1.0493 ?>">
                                        <small class="text-muted" style="font-size: 0.65rem;">Base de prestaciones de ley</small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">UMA</label>
                                        <input type="number" step="0.01" name="config[uma]" class="form-control form-control-sm" value="<?= $post['config']['uma'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">S. Mínimo</label>
                                        <input type="number" step="0.01" name="config[salario_minimo]" class="form-control form-control-sm" value="<?= $post['config']['salario_minimo'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">% ISN</label>
                                        <input type="number" step="0.01" name="config[isn_rate]" class="form-control form-control-sm" value="<?= $post['config']['isn_rate'] ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">% Riesgo Trabajo</label>
                                        <input type="number" step="0.00001" name="config[prima_riesgo]" class="form-control form-control-sm" value="<?= $post['config']['prima_riesgo'] ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Aguin.</label>
                                        <input type="number" name="config[dias_aguinaldo]" class="form-control form-control-sm" value="<?= $post['config']['dias_aguinaldo'] ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Vac.</label>
                                        <input type="number" name="config[dias_vacaciones]" class="form-control form-control-sm" value="<?= $post['config']['dias_vacaciones'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Factor Días Mes</label>
                                        <select name="config[dias_mes]" class="form-select form-select-sm">
                                            <option value="30" <?= $post['config']['dias_mes'] == 30 ? 'selected' : '' ?>>30 (Comercial)</option>
                                            <option value="30.4" <?= $post['config']['dias_mes'] == 30.4 ? 'selected' : '' ?>>30.4 (Promedio)</option>
                                            <option value="30.4166" <?= $post['config']['dias_mes'] == 30.4166 ? 'selected' : '' ?>>30.4166 (Exacto)</option>
                                        </select>
                                        <small class="text-muted" style="font-size: 0.65rem;">Factor para obtener Sueldo Diario</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">Calcular Proyecto Completo</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($grupos)): ?>
                <div class="card shadow border-0 mb-5 bg-victoria text-white">
                    <div class="card-body p-5">
                        <div class="row align-items-center text-center text-md-start">
                            <div class="col-md-7">
                                <h6 class="text-uppercase fw-bold text-info mb-2">Impacto Financiero Mensual</h6>
                                <h1 class="display-4 fw-bold mb-0">Ahorro: <span class="text-warning">$<?= number_format($totales['ahorro'], 2) ?></span></h1>
                                <p class="lead opacity-75">Optimización del <?= number_format((1 - ($totales['propuesta'] / $totales['actual'])) * 100, 1) ?>% sobre el costo tradicional.</p>
                            </div>
                            <div class="col-md-5 border-start border-white-25">
                                <div class="ps-md-4">
                                    <small class="d-block opacity-75 mb-1">COSTO ACTUAL (100% NÓMINA)</small>
                                    <h4 class="fw-light text-decoration-line-through text-danger">$<?= number_format($totales['actual'], 2) ?></h4>
                                    <small class="d-block opacity-75 mt-3 mb-1">COSTO CON FOR</small>
                                    <h3 class="fw-bold text-success">$<?= number_format($totales['propuesta'], 2) ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-4 fw-bold text-dark"><i class="bi bi-list-stars text-primary me-2"></i> Desglose por Grupos</h4>

                <div class="accordion shadow-sm" id="accordionResultados">
                    <?php foreach ($grupos as $i => $g): ?>
                        <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>">
                                    <div class="d-flex justify-content-between w-100 align-items-center pe-3">
                                        <span>
                                            <strong class="text-primary me-2">Grupo <?= $i + 1 ?>:</strong> 
                                            <?= $g['qty'] ?> empleado(s) | Neto c/u: $<?= number_format($g['individual_legal']['neto'], 2) ?>
                                        </span>
                                        <span class="badge bg-success py-2 px-3 shadow-sm">Ahorro: $<?= number_format(($g['subtotal_legal'] - $g['subtotal_prop']), 2) ?></span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#accordionResultados">
                                <div class="accordion-body bg-light border-top">
                                    <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="p-4 border-0 rounded bg-white h-100 shadow-sm">
                                            <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.85rem;">Escenario Tradicional (Individual)</h6>
                                            
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="fw-bold text-dark">Sueldo Bruto:</span>
                                                <span class="fw-bold text-dark fs-5">$<?= number_format($g['individual_legal']['bruto'], 2) ?></span>
                                            </div>
                                            <div class="mb-3 pb-3 border-bottom">
                                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                                    <span>Sueldo Diario Nominal:</span>
                                                    <span>$<?= number_format($g['individual_legal']['sueldo_diario_nominal'], 2) ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                                    <span>SBC (Base Cotización):</span>
                                                    <span>$<?= number_format($g['individual_legal']['sbc'], 2) ?></span>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <small class="text-muted fw-bold d-block mb-2 text-uppercase" style="font-size: 0.75rem;">Desglose de Carga Patronal</small>
                                                
                                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                                    <span>IMSS Empresa:</span>
                                                    <span>$<?= number_format($g['individual_legal']['imss_patron'], 2) ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                                    <span>INFONAVIT:</span>
                                                    <span>$<?= number_format($g['individual_legal']['infonavit'], 2) ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-3 small text-muted pb-3 border-bottom">
                                                    <span>ISN (<?= $post['config']['isn_rate'] ?>%):</span>
                                                    <span>$<?= number_format($g['individual_legal']['isn'], 2) ?></span>
                                                </div>
                                            </div>

                                            <div class="mt-2 text-muted">
                                                <div class="d-flex justify-content-between mb-1 small">
                                                    <span>Provisiones (Aguinaldo/PV):</span>
                                                    <span>$<?= number_format($g['individual_legal']['provisiones'], 2) ?></span>
                                                </div>
                                            </div>

                                            <hr class="my-3">
                                            
                                            <div class="d-flex justify-content-between text-primary fw-bold fs-5 mt-3">
                                                <span>GASTO REAL TOTAL:</span>
                                                <span>$<?= number_format($g['individual_legal']['gasto_real'], 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="col-lg-6">
                                            <div class="p-4 border-0 rounded text-white h-100 shadow-sm" style="background-color: #1e3a8a;">
                                                <h6 class="fw-bold mb-4 border-bottom pb-3 border-white-25 text-uppercase" style="font-size: 0.85rem;">
                                                    Propuesta <?= ucfirst($g['individual_prop']['tipo_servicio']) ?>
                                                </h6>
                                                <table class="table table-borderless mb-0 text-white">
                                                    <tr><td class="py-2">Costo Fiscal (Nómina)</td><td class="text-end py-2">$<?= number_format($g['individual_prop']['fiscal']['flujo_efectivo'], 2) ?></td></tr>
                                                    <tr><td class="py-2">Complemento</td><td class="text-end py-2">$<?= number_format($g['individual_prop']['complemento'], 2) ?></td></tr>
                                                    <tr><td class="py-2">Comisión</td><td class="text-end py-2">$<?= number_format($g['individual_prop']['comision'], 2) ?></td></tr>
                                                    <tr class="fw-bold text-warning border-top border-white-25"><td class="py-3 fs-5">Costo FOR p/u</td><td class="text-end py-3 fs-5">$<?= number_format($g['individual_prop']['total_propuesta'], 2) ?></td></tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<template id="fila-molde">
    <tr>
        <td>
            <input type="number" name="filas[IDX][cantidad]" class="form-control" value="1" min="1">
        </td>
        <td>
            <input type="number" step="0.01" name="filas[IDX][monto]" class="form-control" placeholder="0.00" required>
        </td>
        
        <!-- CAMPO: Sueldo Bruto Manual para el molde -->
        <td>
            <input type="number" step="0.01" name="filas[IDX][sueldo_bruto_manual]" class="form-control" placeholder="Ej. 30453.63">
        </td>
        
        <!-- CAMPO: SBC Manual para el molde -->
        <td>
            <input type="number" step="0.01" name="filas[IDX][sbc_manual]" class="form-control" placeholder="Ej. 1051.15">
        </td>

        <td>
            <select name="filas[IDX][tipo_calculo]" class="form-select">
                <option value="diario">Diario</option>
                <option value="neto" selected>Mensual</option>
            </select>
        </td>
        <td>
            <select name="filas[IDX][tipo_servicio]" class="form-select">
                <option value="especializado">Especializado</option>
                <option value="proem" selected>PROEM</option>
            </select>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <select name="filas[IDX][modo_nomina]" class="form-select bg-light" style="max-width: 65px; font-size: 0.7rem;">
                    <option value="pct">%</option>
                    <option value="sdi">SDI</option>
                </select>
                <input type="number" step="0.01" name="filas[IDX][valor_nomina]" class="form-control" value="40">
            </div>
        </td>
        <td>
            <input type="number" step="0.1" name="filas[IDX][pct_comision]" class="form-control" value="5.0">
        </td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm remove-row shadow-sm"><i class="bi bi-trash"></i></button>
        </td>
    </tr>
</template>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function() {
        const btnAdd = document.getElementById('add-row');
        const tabla = document.getElementById('tabla-proyectos');
        const template = document.getElementById('fila-molde');

        if (!btnAdd || !tabla || !template) return;

        btnAdd.addEventListener('click', function(e) {
            e.preventDefault();
            const tbody = tabla.querySelector('tbody');
            const indice = tbody.querySelectorAll('tr').length;
            const nuevaFila = template.content.cloneNode(true);

            nuevaFila.querySelectorAll('input, select').forEach(campo => {
                if (campo.name) campo.name = campo.name.replace('IDX', indice);
            });
            tbody.appendChild(nuevaFila);
        });

        tabla.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                const filas = tabla.querySelectorAll('tbody tr');
                if (filas.length > 1) e.target.closest('tr').remove();
            }
        });
    })();
</script>
<?php if (isset($hubo_topes) && $hubo_topes): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'info',
            title: 'Ajuste Legal Automático',
            html: 'Algunos porcentajes o SDI ingresados generaban un sueldo inferior al <b>Salario Mínimo de Ley</b>.<br><br>El sistema ha ajustado automáticamente los campos al mínimo permitido para garantizar el cumplimiento fiscal (Cero retenciones al empleado).',
            confirmButtonColor: '#1e3a8a',
            confirmButtonText: 'Entendido'
        });
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>