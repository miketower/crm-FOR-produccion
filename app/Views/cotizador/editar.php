<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<style>
    /* Clases para que custom.js pueda mostrar/ocultar los planes */
    .collapsible-content { display: none; }
    .collapsible-content.is-open { display: block; animation: fadeIn 0.4s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Editar Propuesta #<?= esc($propuesta['id']) ?></h2>
        <p class="text-muted small">Modifica los parámetros y actualiza la cotización.</p>
    </div>
    <a href="<?= base_url('cotizador') ?>" class="btn btn-outline-secondary shadow-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Cancelar
    </a>
</div>

<form id="formProyecto">
    <input type="hidden" id="propuesta_id" value="<?= esc($propuesta['id']) ?>">

    <!-- 1. DATOS GENERALES -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-4">
            <h5 class="fw-bold text-primary border-bottom pb-2 mb-4"><i class="bi bi-building me-2"></i>Datos Generales del Proyecto</h5>

            <div class="row g-4">
                <div class="col-md-8">
                    <label class="small text-muted fw-bold mb-2">Nombre de la Empresa</label>
                    <input type="text" id="nombre_cliente" class="form-control bg-light border-0" value="<?= esc($propuesta['nombre_cliente'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="small text-muted fw-bold mb-2 d-block">Diagnóstico 360 Inicial</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="diagInicial" style="transform: scale(1.2); margin-left: -1.5em;" <?= ($propuesta['diag_360_inicial'] ?? 0) == 1 ? 'checked' : '' ?>>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="small text-muted fw-bold mb-2">Fecha</label>
                    <input type="date" id="fecha_propuesta" class="form-control bg-light border-0" value="<?= esc($propuesta['fecha_propuesta'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="small text-muted fw-bold mb-2">Solicitante</label>
                    <input type="text" id="nombre_solicitante" class="form-control bg-light border-0" value="<?= esc($propuesta['solicitante'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label class="small text-muted fw-bold mb-2">Cargo</label>
                    <input type="text" id="cargo_solicitante" class="form-control bg-light border-0" value="<?= esc($propuesta['cargo_solicitante'] ?? '') ?>">
                </div>

                <div class="col-12">
                    <label class="small text-muted fw-bold mb-2">Impresión Diagnóstica</label>
                    <textarea id="impresion_diagnostica" class="form-control bg-light border-0" rows="3"><?= esc($propuesta['impresion_diagnostica'] ?? '') ?></textarea>
                </div>

                <div class="col-12 mt-4">
                    <label class="small text-muted fw-bold mb-3 d-block text-uppercase">¿Requiere Intervención?</label>
                    <div class="d-flex gap-4 p-3 rounded bg-light border">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reqIntervencion" id="intSi" value="Si" <?= ($propuesta['req_intervencion'] ?? '') === 'Si' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="intSi">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reqIntervencion" id="intNo" value="No" <?= ($propuesta['req_intervencion'] ?? '') === 'No' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="intNo">No</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reqIntervencion" id="intParcial" value="Parcial" <?= ($propuesta['req_intervencion'] ?? '') === 'Parcial' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="intParcial">Parcial</label>
                        </div>
                    </div>
                </div>

                <div id="wrapperEspecifique" class="col-12" style="display: <?= in_array($propuesta['req_intervencion'] ?? '', ['No', 'Parcial']) ? 'block' : 'none' ?>;">
                    <label class="small text-muted fw-bold mb-2">Especifique ¿por qué?</label>
                    <textarea id="motivo_intervencion" class="form-control bg-light border-0" rows="2"><?= esc($propuesta['motivo_intervencion'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. NIVEL DE RIESGO -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-speedometer2 me-2"></i>Nivel de Riesgo</h5>
                    <p class="text-muted small mb-0">Evaluación de complejidad y criticidad</p>
                </div>
                <div id="risk-value-display" class="h2 fw-bold mb-0 text-primary"><?= esc($propuesta['nivel_riesgo'] ?? 0) ?></div>
            </div>
            <div class="py-3">
                <input type="range" class="form-range" min="0" max="3" step="1" value="<?= esc($propuesta['nivel_riesgo'] ?? 0) ?>" id="riskRange" oninput="updateRiskLevel(this.value)">
                <div class="d-flex justify-content-between mt-2 px-1 small fw-bold text-muted">
                    <span>Bajo</span>
                    <span>Medio</span>
                    <span>Alto</span>
                    <span>Crítico</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MARKET BENCHMARK -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-bar-chart-line me-2"></i>Market Benchmark</h5>
                    <p class="text-muted small mb-0">Comparativa de salarios por perfil profesional</p>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="switchBenchmark" onchange="toggleBenchmark()" style="transform: scale(1.3);" <?= !empty($benchmarks) ? 'checked' : '' ?>>
                </div>
            </div>

            <div id="benchmarkFields" style="display: <?= !empty($benchmarks) ? 'block' : 'none' ?>;" class="mt-4 pt-3 border-top">
                <div id="lista-benchmarks">
                    <?php if(!empty($benchmarks)): ?>
                        <?php foreach($benchmarks as $b): ?>
                            <div class="benchmark-row mb-3 p-3 bg-light rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">PERFIL</span>
                                    <button type="button" class="btn btn-sm text-danger fw-bold" onclick="this.closest('.benchmark-row').remove()">ELIMINAR</button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12 mb-2">
                                        <label class="small text-muted fw-bold">NOMBRE DEL PUESTO / PERFIL</label>
                                        <input type="text" name="bench_puesto[]" class="form-control bg-white" value="<?= esc($b['puesto']) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small d-block mb-1">25° Percentil</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white">$</span>
                                            <input type="number" step="0.01" name="bench_25[]" class="form-control bg-white" value="<?= esc($b['percentile_25']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small d-block mb-1">Promedio</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white">$</span>
                                            <input type="number" step="0.01" name="bench_mean[]" class="form-control bg-white" value="<?= esc($b['mean']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small d-block mb-1">Mediana</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white">$</span>
                                            <input type="number" step="0.01" name="bench_median[]" class="form-control bg-white" value="<?= esc($b['median']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small d-block mb-1">75° Percentil</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white">$</span>
                                            <input type="number" step="0.01" name="bench_75[]" class="form-control bg-white" value="<?= esc($b['percentile_75']) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2 fw-bold" onclick="window.agregarFilaBenchmark()">
                    <i class="bi bi-plus-circle me-1"></i> AGREGAR OTRO PUESTO
                </button>
            </div>
        </div>
    </div>

    <!-- 4. ESCENARIO ACTUAL -->
    <div class="card shadow-sm border-0 mb-4 bg-light border-start border-warning border-4">
        <div class="card-body py-4">
            <h5 class="fw-bold text-dark mb-0">Escenario Actual Real</h5>
            <p class="text-muted small mb-3">Capture los conceptos de la operación actual del cliente.</p>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">1. SALARIOS BRUTOS</label>
                    <input type="number" id="actual_bruto" class="form-control" value="<?= esc($propuesta['actual_bruto'] ?? '') ?>" oninput="calcularBaselineActual()">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">2. SUELDOS NETOS (INFO)</label>
                    <input type="number" id="actual_nomina" class="form-control" value="<?= esc($propuesta['actual_nomina'] ?? '') ?>" oninput="calcularBaselineActual()">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">3. EXCEDENTE</label>
                    <input type="number" id="actual_excedente" class="form-control" value="<?= esc($propuesta['actual_excedente'] ?? '') ?>" oninput="calcularBaselineActual()">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">4. COSTO SOCIAL</label>
                    <input type="number" id="actual_carga" class="form-control" value="<?= esc($propuesta['actual_carga'] ?? '') ?>" oninput="calcularBaselineActual()">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">5. COMISIÓN ACTUAL</label>
                    <input type="number" id="actual_comision" class="form-control" value="<?= esc($propuesta['actual_comision'] ?? '') ?>" oninput="calcularBaselineActual()">
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-white rounded shadow-sm text-center border">
                        <p class="small text-muted mb-1 fw-bold">TOTAL ACTUAL</p>
                        <h4 id="total_actual_display" class="mb-0 text-dark fw-bold">$ <?= number_format($propuesta['actual_total'] ?? 0, 2) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. PLANES Y COMISION -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body">
                    <label class="small text-muted fw-bold mb-3 d-block text-uppercase">PLAN INT. REQUERIDO</label>
                    <div class="d-flex flex-wrap gap-2">
                        <input type="radio" name="plan_integral" id="plan_proem" value="Proem" class="btn-check" <?= ($propuesta['plan_requerido'] ?? '') === 'Proem' ? 'checked' : '' ?> onclick="gestionarVisibilidadPlanes();">
                        <label class="btn btn-outline-primary" for="plan_proem">Proem</label>

                        <input type="radio" name="plan_integral" id="plan_especializado" value="Servicio Especializado" class="btn-check" <?= ($propuesta['plan_requerido'] ?? '') === 'Servicio Especializado' ? 'checked' : '' ?> onclick="gestionarVisibilidadPlanes();">
                        <label class="btn btn-outline-primary" for="plan_especializado">Servicio Especializado</label>

                        <input type="radio" name="plan_integral" id="plan_mixto" value="Mixto" class="btn-check" <?= ($propuesta['plan_requerido'] ?? '') === 'Mixto' ? 'checked' : '' ?> onclick="gestionarVisibilidadPlanes();">
                        <label class="btn btn-outline-primary" for="plan_mixto">Mixto</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body">
                    <label class="small text-muted fw-bold mb-3 d-block text-uppercase">COMISIÓN (%)</label>
                    <div class="input-group">
                        <input type="number" name="comision_porcentaje" id="comision_porcentaje" class="form-control text-center" value="<?= esc($propuesta['comision_pct'] ?? '0') ?>" step="0.1" min="0" oninput="calcularProemFinal(); calcularEspecializado();">
                        <span class="input-group-text bg-light fw-bold">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6A. SECCIÓN PROEM -->
    <div id="seccion_calculo_proem" class="collapsible-content">
        <div class="card shadow-sm border-0 mb-4 bg-white border-start border-primary border-4">
            <div class="card-body py-4">
                <h5 class="fw-bold text-primary mb-4 text-center">COMPARATIVA DE ESTRATEGIA PROEM</h5>
                
                <div class="row g-4">
                    <div class="col-12 mb-3">
                        <label class="small text-muted fw-bold d-block mb-2 text-uppercase">Plantilla PROEM</label>
                        <div id="contenedor_perfiles_proem">
                            <?php if(!empty($perfiles)): foreach($perfiles as $perf): if($perf['tipo_plan'] == 'proem'): ?>
                                <div class="row g-2 mb-2 align-items-center animate-fadeIn">
                                    <div class="col-3">
                                        <input type="number" name="proem_cant[]" class="form-control form-control-sm bg-white" value="<?= esc($perf['cantidad']) ?>">
                                    </div>
                                    <div class="col-7">
                                        <input type="text" name="proem_cargo[]" class="form-control form-control-sm bg-white" value="<?= esc($perf['cargo']) ?>">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-sm text-danger" onclick="this.parentElement.parentElement.remove()">×</button>
                                    </div>
                                </div>
                            <?php endif; endforeach; endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarFilaPerfil('proem')">
                            <i class="bi bi-plus"></i> Agregar Cargo
                        </button>
                    </div>

                    <div class="col-md-6 border-end">
                        <p class="text-center fw-bold text-muted mb-3 text-uppercase">Escenario 100% IMSS</p>
                        <div class="mb-3">
                            <label class="small text-muted">1. SALARIO BRUTO</label>
                            <input type="number" id="base_100" class="form-control bg-light" value="<?= esc($proem['base_100'] ?? '') ?>" oninput="calcularProemFinal()">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">2. SALARIO NETO</label>
                            <input type="number" id="neto_100" class="form-control bg-light" value="<?= esc($proem['neto_100'] ?? '') ?>" oninput="calcularProemFinal()">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">3. CARGA SOCIAL</label>
                            <input type="number" id="carga_100" class="form-control bg-light" value="<?= esc($proem['carga_100'] ?? '') ?>" oninput="calcularProemFinal()">
                        </div>
                        <div class="p-3 bg-light rounded text-center mt-4 border">
                            <p class="small text-muted mb-1 fw-bold">COSTO 100% IMSS</p>
                            <h4 id="total_100_display" class="mb-0 text-dark fw-bold">$ 0.00</h4>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <p class="text-center fw-bold text-primary mb-3 text-uppercase">Escenario PROEM</p>
                        <div class="mb-3">
                            <label class="small text-muted">1. SALARIO BRUTO (PROEM)</label>
                            <input type="number" id="bruto_proem" class="form-control" value="<?= esc($proem['bruto_proem'] ?? '') ?>" oninput="calcularProemFinal()">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">2. SALARIO NETO (PROEM)</label>
                            <input type="number" id="neto_proem" class="form-control" value="<?= esc($proem['neto_proem'] ?? '') ?>" oninput="calcularProemFinal()">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">3. CARGA SOCIAL (PROEM)</label>
                            <input type="number" id="carga_proem" class="form-control" value="<?= esc($proem['carga_proem'] ?? '') ?>" oninput="calcularProemFinal()">
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-center mt-4 border border-primary">
                            <p class="small text-primary mb-1 fw-bold">TOTAL PROEM</p>
                            <h4 id="total_proem_display" class="mb-0 text-primary fw-bold">$ 0.00</h4>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 g-2 text-center">
                    <div class="col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <p class="small text-muted mb-0 fw-bold">Excedente</p>
                            <h6 id="excedente_val" class="text-dark mb-0">$ 0.00</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <p class="small text-muted mb-0 fw-bold">Comisión (Monto)</p>
                            <h6 id="comision_monto_val" class="text-dark mb-0">$ 0.00</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 rounded bg-success bg-opacity-10 border border-success">
                            <p class="small text-success mb-0 fw-bold">Ahorro Mensual</p>
                            <h6 id="ahorro_mensual_val" class="text-success fw-bold mb-0">$ 0.00</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 rounded bg-success text-white shadow-sm">
                            <p class="small mb-0 fw-bold">Ahorro Anual</p>
                            <h6 id="ahorro_anual_val" class="fw-bold mb-0">$ 0.00</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6B. SECCIÓN ESPECIALIZADO -->
    <div id="seccion_calculo_especializado" class="collapsible-content">
        <div class="card shadow-sm border-0 mb-4 bg-white border-start border-info border-4">
            <div class="card-body py-4">
                <h5 class="fw-bold text-info mb-4 text-center">COMPARATIVA SERVICIO ESPECIALIZADO</h5>
                
                <div class="row g-4">
                    <div class="col-12 mb-3">
                        <label class="small text-muted fw-bold d-block mb-2 text-uppercase">Plantilla Especializado</label>
                        <div id="contenedor_perfiles_esp">
                            <?php if(!empty($perfiles)): foreach($perfiles as $perf): if($perf['tipo_plan'] == 'esp'): ?>
                                <div class="row g-2 mb-2 align-items-center animate-fadeIn">
                                    <div class="col-3">
                                        <input type="number" name="esp_cant[]" class="form-control form-control-sm bg-white" value="<?= esc($perf['cantidad']) ?>">
                                    </div>
                                    <div class="col-7">
                                        <input type="text" name="esp_cargo[]" class="form-control form-control-sm bg-white" value="<?= esc($perf['cargo']) ?>">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-sm text-danger" onclick="this.parentElement.parentElement.remove()">×</button>
                                    </div>
                                </div>
                            <?php endif; endforeach; endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="agregarFilaPerfil('esp')">
                            <i class="bi bi-plus"></i> Agregar Cargo
                        </button>
                    </div>

                    <div class="col-md-6 border-end">
                        <p class="text-center fw-bold text-muted mb-3 text-uppercase">Escenario 100% IMSS</p>
                        <div class="mb-3">
                            <label class="small text-muted">SALARIO BRUTO (BASE)</label>
                            <input type="number" id="esp_base_100" class="form-control bg-light" value="<?= esc($esp['base_100'] ?? '') ?>" oninput="calcularEspecializado()">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">SALARIO NETO</label>
                            <input type="number" id="esp_neto_100" class="form-control bg-light" value="<?= esc($esp['neto_100'] ?? '') ?>" oninput="calcularEspecializado()">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">CARGA SOCIAL (100% IMSS)</label>
                            <input type="number" id="esp_carga_100" class="form-control bg-light" value="<?= esc($esp['carga_100'] ?? '') ?>" oninput="calcularEspecializado()">
                        </div>
                        <div class="p-3 bg-light rounded text-center mt-4 border">
                            <p class="small text-muted mb-1 fw-bold">COSTO 100% IMSS</p>
                            <h4 id="res_esp_100_total" class="mb-0 text-dark fw-bold">$ 0.00</h4>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <p class="text-center fw-bold text-info mb-3 text-uppercase">Escenario Especializado</p>
                        <div class="mb-3">
                            <label class="small text-muted">SUELDOS (FOR)</label>
                            <input type="number" id="esp_sueldos_for" class="form-control" value="<?= esc($esp['sueldos_for'] ?? '') ?>" oninput="calcularEspecializado()">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted">CARGA SOCIAL (FOR)</label>
                            <input type="number" id="esp_carga_for" class="form-control" value="<?= esc($esp['carga_for'] ?? '') ?>" oninput="calcularEspecializado()">
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-center border border-info" style="margin-top: 5rem;">
                            <p class="small text-info mb-1 fw-bold">TOTAL ESPECIALIZADO</p>
                            <h4 id="res_esp_for_total" class="mb-0 text-info fw-bold">$ 0.00</h4>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 g-2 text-center justify-content-center">
                    <div class="col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <p class="small text-muted mb-0 fw-bold">Comisión (Monto)</p>
                            <h6 id="res_esp_comision_monto" class="text-dark mb-0">$ 0.00</h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="banner_esp_resultado" class="p-2 rounded text-white shadow-sm" style="background: #333;">
                            <p class="small mb-0 fw-bold">DIFERENCIA</p>
                            <h6 id="res_esp_diferencia" class="fw-bold mb-0">$ 0.00</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-2 rounded bg-success text-white shadow-sm">
                            <p class="small mb-0 fw-bold">Ahorro Anual</p>
                            <h6 id="esp_ahorro_anual_val" class="fw-bold mb-0">$ 0.00</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. PERIODO PAGO -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <label class="small text-muted fw-bold mb-3 d-block text-uppercase">PERIODO DE PAGO</label>
            <div class="d-flex flex-wrap gap-2">
                <input type="radio" name="periodo_pago" id="pago_semanal" value="Semanal" class="btn-check" <?= ($propuesta['periodo_pago'] ?? '') === 'Semanal' ? 'checked' : '' ?>>
                <label class="btn btn-outline-secondary" for="pago_semanal">Semanal</label>

                <input type="radio" name="periodo_pago" id="pago_catorcenal" value="Catorcenal" class="btn-check" <?= ($propuesta['periodo_pago'] ?? '') === 'Catorcenal' ? 'checked' : '' ?>>
                <label class="btn btn-outline-secondary" for="pago_catorcenal">Catorcenal</label>

                <input type="radio" name="periodo_pago" id="pago_quincenal" value="Quincenal" class="btn-check" <?= ($propuesta['periodo_pago'] ?? '') === 'Quincenal' ? 'checked' : '' ?>>
                <label class="btn btn-outline-secondary" for="pago_quincenal">Quincenal</label>

                <input type="radio" name="periodo_pago" id="pago_mensual" value="Mensual" class="btn-check" <?= ($propuesta['periodo_pago'] ?? '') === 'Mensual' ? 'checked' : '' ?>>
                <label class="btn btn-outline-secondary" for="pago_mensual">Mensual</label>
            </div>
        </div>
    </div>

    <!-- 8. OPCIONES FIJADAS -->
    <div id="opciones_fijadas_container" class="card shadow-sm border-warning border-2 mb-4 bg-light" style="display: none;">
        <div class="card-body text-center">
            <h6 class="text-warning fw-bold mb-3 text-uppercase">Opciones incluidas en esta propuesta:</h6>
            <div id="lista_opciones_fijadas" class="d-flex flex-wrap justify-content-center gap-2"></div>
            <button type="button" class="btn btn-sm btn-outline-danger mt-3 fw-bold" onclick="limpiarOpciones()">
                <i class="bi bi-trash"></i> Borrar opciones y reiniciar
            </button>
        </div>
    </div>

    <!-- BOTONES FINALES -->
    <div class="text-center mb-5 d-flex justify-content-center gap-3 flex-wrap mt-4">
        <button type="button" class="btn btn-warning fw-bold shadow-sm" onclick="fijarOpcionActual()">
            <i class="bi bi-pin-angle-fill me-1"></i> FIJAR ESTA OPCIÓN
        </button>
        <button type="button" class="btn btn-outline-primary fw-bold shadow-sm" onclick="actualizarCotizacionExistente(false)"> 
            <i class="bi bi-save me-1"></i> SOLO ACTUALIZAR
        </button>
        <button type="button" class="btn btn-primary fw-bold shadow-sm" onclick="actualizarCotizacionExistente(true)"> 
            <i class="bi bi-file-earmark-pdf me-1"></i> ACTUALIZAR Y GENERAR PDF
        </button>
    </div>
</form>

<!-- CONTENEDOR OCULTO PARA EL PDF -->
<div id="plantilla_pdf_container" style="display: none;">
    <div id="contenido_pdf" class="p-3" style="background: #fff; color: #000;">
    
        <div style="margin-bottom: 15px;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                <tr>
                    <td style="width: 50%; text-align: left; vertical-align: bottom; border-bottom: 3px solid #20FF7E; padding-bottom: 12px;">
                        <img src="<?= base_url('img/logo-fn.png') ?>" alt="FOR" style="width: 140px; height: auto; display: inline-block;">
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: bottom; border-bottom: 3px solid #20FF7E; padding-bottom: 12px;">
                        <h2 style="margin: 0 0 8px 0; font-size: 22px; font-weight: bold; color: #333; line-height: 1;">PROPUESTA ESTRATÉGICA</h2>
                        <p id="pdf_fecha_impresion" style="margin: 0; font-size: 12px; color: #666;"></p>
                    </td>
                </tr>
            </table>
        </div>

        <div style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 6px 10px; margin-bottom: 12px;">
            <table style="width: 100%; font-size: 11px; font-family: Arial, sans-serif; border-collapse: collapse; line-height: 1.3;">
                <tr>
                    <td style="padding: 2px 5px 2px 0; width: 50%;"><strong>Cliente:</strong> <span id="pdf_cliente"></span></td>
                    <td style="padding: 2px 0 2px 5px; width: 50%;"><strong>Solicitante:</strong> <span id="pdf_solicitante"></span></td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px 2px 0;"><strong>Nivel de Riesgo:</strong> <span id="pdf_nivel_riesgo"></span></td>
                    <td style="padding: 2px 0 2px 5px;"><strong>Periodo de Pago:</strong> <span id="pdf_periodo_pago"></span></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 2px 0;"><strong>Impresión Diagnóstica:</strong> <span id="pdf_impresion"></span></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 2px 0;" id="pdf_resumen_plantillas"><strong>Plantilla:</strong> </td>
                </tr>
            </table>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
            <thead id="pdf_mega_head"></thead>
            <tbody id="pdf_mega_body"></tbody>
        </table>

        <div style="margin-top: 15px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #333; font-family: Arial, sans-serif;">
            <h4 style="margin: 0 0 5px 0; font-size: 13px; color: #000; font-weight: bold;">Condiciones Comerciales</h4>
            <ul style="margin: 0; padding-left: 20px; font-size: 11px; color: #444; line-height: 1.4;">
                <li>Los montos expresados son en Moneda Nacional (MXN) y no incluyen I.V.A.</li>
                <li>Esta propuesta tiene una vigencia de 15 días a partir de la fecha de emisión.</li>
                <li>El porcentaje de comisión aplica según lo estipulado en el contrato de prestación de servicios.</li>
            </ul>
        </div>
    </div>
</div>

<script>
    const BASE_URL = '<?= base_url() ?>';
</script>
<script src="<?= base_url('js/custom.js') ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof gestionarVisibilidadPlanes === 'function') gestionarVisibilidadPlanes();
        if(typeof calcularBaselineActual === 'function') calcularBaselineActual();
        if(typeof calcularProemFinal === 'function') calcularProemFinal();
        if(typeof calcularEspecializado === 'function') calcularEspecializado();
    });
</script>

<?= $this->endSection() ?>