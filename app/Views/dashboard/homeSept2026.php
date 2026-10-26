<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark"><?php echo $titulo; ?></h4>
        <small class="text-muted">Periodo: 1 Enero 2026 - 7 Sep 2026</small>
    </div>
    <nav class="nav nav-pills shadow-sm p-1 bg-white rounded border small">
        <a class="nav-link py-1 px-3 <?php echo ($q == '1') ? 'active' : ''; ?>" href="?q=1">Año 2026</a>
    </nav>
</div>

<!-- Navegación de Pestañas (Tabs) -->
<ul class="nav nav-pills mb-4 p-2 bg-white shadow-sm rounded border d-flex gap-2" id="dashboardTabs" role="tablist">
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link active w-100 fw-bold fs-6" id="financiero-tab" data-bs-toggle="pill" data-bs-target="#financiero" type="button" role="tab" aria-controls="financiero" aria-selected="true">
            <i class="bi bi-cash-stack me-2"></i>Métricas Financieras
        </button>
    </li>
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link w-100 fw-bold fs-6" id="actividad-tab" data-bs-toggle="pill" data-bs-target="#actividad" type="button" role="tab" aria-controls="actividad" aria-selected="false">
            <i class="bi bi-funnel-fill me-2"></i>Citas, Propuestas y Cierres
        </button>
    </li>
</ul>

<!-- Contenido de las Pestañas -->
<div class="tab-content" id="dashboardTabsContent">
    
    <!-- ============================================== -->
    <!-- PESTAÑA 1: FINANZAS Y PROYECCIONES             -->
    <!-- ============================================== -->
    <div class="tab-pane fade show active" id="financiero" role="tabpanel" aria-labelledby="financiero-tab">
        
        <div class="row g-3 mb-4 text-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3 h-100" style="border-top: 4px solid #198754;">
                    <small class="text-muted fw-bold small text-uppercase">Ingreso Bruto</small>
                    <h3 class="fw-bold mb-0 text-success">$<?php echo number_format($montoCobradoTotal, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3 h-100" style="border-top: 4px solid #0d6efd;">
                    <small class="text-muted fw-bold small text-uppercase">Facturación</small>
                    <h3 class="fw-bold mb-0 text-primary">$<?php echo number_format($montoVendidoTotal, 2); ?></h3>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-bar-chart-fill me-2"></i>Desempeño Financiero</h5>
                        <div id="chart-montos-vs"></div>
                        
                        <hr class="my-4">
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <h6 class="small fw-bold text-muted">Venta por Canal</h6>
                                <div id="pie-vendido"></div>
                            </div>
                            <div class="col-6">
                                <h6 class="small fw-bold text-muted">Cobro por Canal</h6>
                                <div id="pie-cobrado"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted text-uppercase small text-center mb-3">Avance Objetivo 100M</h6>
                        <div class="row">
                            <div class="col-12 text-center">
                                <small class="text-muted d-block small">Avance Periodo</small>
                                <div id="chart-meta-periodo"></div>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <?php 
                            $coloresCanal = array('text-primary', 'text-success', 'text-warning');
                            foreach ($canalesLabels as $i => $label): 
                                $monto = $dataVendidoCanal[$i];
                                $pct = $contribucionCanales[$i];
                            ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small">
                                    <i class="bi bi-circle-fill me-2 <?php echo $coloresCanal[$i]; ?>"></i>
                                    <strong><?php echo $label; ?></strong>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold small">$<?php echo number_format($monto, 2); ?></div>
                                    <small class="text-muted" style="font-size: 10px;"><?php echo $pct; ?>% del año</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================== -->
    <!-- PESTAÑA 2: ACTIVIDAD Y EMBUDO                  -->
    <!-- ============================================== -->
    <div class="tab-pane fade" id="actividad" role="tabpanel" aria-labelledby="actividad-tab">
        
        <div class="row g-3 mb-4 text-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3 h-100" style="border-top: 4px solid #ffc107;">
                    <small class="text-muted fw-bold small text-uppercase">Citas</small>
                    <h3 class="fw-bold mb-0"><?php echo array_sum($dataCitas); ?></h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3 h-100" style="border-top: 4px solid #6f42c1;">
                    <small class="text-muted fw-bold small text-uppercase">Cierres</small>
                    <h3 class="fw-bold mb-0"><?php echo array_sum($dataCierres); ?></h3>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECCIÓN: DESEMPEÑO DE CITAS AGOSTO         -->
        <!-- ========================================== -->
        <div class="row g-4 mb-4">
            <!-- AGOSTO COMPLETO -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-primary text-center"><i class="bi bi-calendar-check me-2"></i>Progreso Citas Septiembre 2026</h5>
                        
                        <div class="text-center mb-3">
                            <span class="display-5 fw-bold text-dark"><?= $julioCitasActual ?></span>
                            <span class="fs-5 text-muted">/ <?= $julioCitasMeta ?> Citas</span>
                        </div>
                        
                        <div class="progress mb-4" style="height: 25px; border-radius: 12px;">
                            <?php $porcentajeJulio = round(($julioCitasActual/$julioCitasMeta)*100); ?>
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentajeJulio ?>%;" aria-valuenow="<?= $julioCitasActual ?>" aria-valuemin="0" aria-valuemax="<?= $julioCitasMeta ?>">
                                <?= $porcentajeJulio ?>% Alcanzado
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted text-center small mb-3">Distribución por Canal (Agosto)</h6>
                        <div id="chart-citas-julio"></div>
                    </div>
                </div>
            </div>
            
            <!-- SEMANA DE AGOSTO -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-info text-center"><i class="bi bi-calendar3-week me-2"></i><?= $tituloSemanaCitas ?></h5>
                        
                        <div class="text-center mb-3">
                            <span class="display-5 fw-bold text-dark"><?= $sem3CitasActual ?></span>
                            <span class="fs-5 text-muted">Citas logradas</span>
                        </div>
                        
                        <h6 class="fw-bold text-muted text-center small mt-4">Distribución por Canal (Semana <?= $semanaMostrar ?>)</h6>
                        <div id="chart-citas-sem3" class="d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4 text-primary text-center"><i class="bi bi-funnel-fill me-2"></i>Conversión Septiembre (Citas vs Propuestas vs Cierres)</h5>
                        <div id="chart-funnel-julio"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body">
                <h6 class="fw-bold mb-4 text-muted">Cumplimiento de Metas: Real vs Proyectado</h6>
                <div id="chart-vs-actividad"></div>
            </div>
        </div>

    </div>
</div>

<script>
    const labels = <?php echo json_encode($canalesLabels); ?>;
    const coloresCanales = ['#0d6efd', '#198754', '#ffc107'];
    const moneyFormat = (v) => "$" + v.toLocaleString('es-MX');

    // 1. Barras Financieras
    new ApexCharts(document.querySelector("#chart-montos-vs"), {
        series: [{ name: 'Monto', data: <?php echo json_encode($dataMontosVs); ?> }],
        chart: { 
            type: 'bar', 
            height: 380, 
            toolbar: { show: false } 
        },
        colors: ['#28a745', '#28a745', '#ffc107', '#28a745', '#ffc107', '#adb5bd'],
        plotOptions: { 
            bar: { 
                horizontal: true, 
                distributed: true, 
                borderRadius: 5, 
                barHeight: '65%', 
                dataLabels: {
                    position: 'center' 
                }
            } 
        },
        dataLabels: {
            enabled: true,
            textAnchor: 'middle',
            formatter: function (val) {
                return "$" + val.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },
            style: {
                fontSize: '13px',
                fontWeight: 'bold',
                colors: ['#000'] 
            },
            dropShadow: { 
                enabled: false 
            }
        },
        xaxis: { 
            categories: [
                'Facturado', 
                'IB Logrado', 
                'IB Proyectado', 
                'IB Nuevo Logrado', 
                'IB Nuevo Proyectado', 
                'Proyectado'
            ],
            labels: {
                formatter: function (val) {
                    if (val >= 1000000) return "$" + (val / 1000000).toFixed(1) + "M";
                    return "$" + val.toLocaleString('en-US');
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold'
                }
            }
        },
        tooltip: { 
            y: { 
                formatter: function(val) {
                    return "$" + val.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            } 
        }
    }).render();

    // 2. Pies Financieros
    const pieCfg = (data) => ({
        series: data, chart: { type: 'pie', height: 200 },
        labels: labels, colors: coloresCanales, legend: { position: 'bottom' },
        tooltip: { y: { formatter: moneyFormat } }
    });
    new ApexCharts(document.querySelector("#pie-vendido"), pieCfg(<?php echo json_encode($dataVendidoCanal); ?>)).render();
    new ApexCharts(document.querySelector("#pie-cobrado"), pieCfg(<?php echo json_encode($dataCobradoCanal); ?>)).render();

    // 3. Cumplimiento de Metas: Real vs Proyectado
    new ApexCharts(document.querySelector("#chart-vs-actividad"), {
        series: [
            { name: 'Real', data: <?php echo json_encode($dataRealAct); ?> },
            { name: 'Proyectado', data: <?php echo json_encode($dataProyectadoAct); ?> }
        ],
        chart: { type: 'bar', height: 300, toolbar: {show:false} },
        colors: ['#0d6efd', '#ced4da'],
        plotOptions: { bar: { dataLabels: { position: 'top' } } },
        dataLabels: { enabled: true, formatter: function(v, o) { 
            if (o.seriesIndex === 0) {
                let meta = o.w.config.series[1].data[o.dataPointIndex];
                return v + " (" + (meta > 0 ? ((v/meta)*100).toFixed(1) : 0) + "%)";
            }
            return v;
        }, offsetY: -20 },
        xaxis: { categories: ['Citas', 'Propuestas', 'Cierres'] }
    }).render();

    // 4. Gráficas Radiales de Avance (Objetivo 100M)
    const renderRadial = (val, elementId, color) => {
        new ApexCharts(document.querySelector(elementId), {
            series: [val],
            chart: { type: 'radialBar', height: 220 },
            plotOptions: {
                radialBar: {
                    hollow: { size: '60%' },
                    dataLabels: {
                        name: { show: false },
                        value: { fontSize: '20px', fontWeight: 'bold', offsetY: 5, formatter: (v) => v + "%" }
                    }
                }
            },
            colors: [color]
        }).render();
    };
    renderRadial(<?php echo $porcentajeMetaAnual; ?>, "#chart-meta-periodo", '#0d6efd');

    // ==========================================
    // GRÁFICAS DE CITAS DESGLOSADAS POR ORIGEN
    // ==========================================
    
    // Gráfica de Barras - Desglose Agosto
    new ApexCharts(document.querySelector("#chart-citas-julio"), {
        series: [{
            name: 'Citas',
            data: <?php echo json_encode($julioDistribucionValores); ?>
        }],
        chart: { type: 'bar', height: 250, toolbar: { show: false } },
        plotOptions: {
            bar: { distributed: true, borderRadius: 4, horizontal: true, dataLabels: { position: 'bottom' } }
        },
        colors: ['#0d6efd', '#198754', '#ffc107', '#0dcaf0'],
        xaxis: { categories: <?php echo json_encode($julioDistribucionLabels); ?> },
        legend: { show: false },
        dataLabels: { 
            enabled: true,
            textAnchor: 'start',
            style: { colors: ['#000'] },
            formatter: function (val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
            },
            offsetX: 0
        },
        yaxis: { show: false }
    }).render();

    // Gráfica de Dona - Desglose Semana Mostrar
    new ApexCharts(document.querySelector("#chart-citas-sem3"), {
        series: <?php echo json_encode($sem3DistribucionValores); ?>,
        chart: { type: 'donut', height: 250 },
        labels: <?php echo json_encode($sem3DistribucionLabels); ?>,
        colors: ['#0d6efd', '#198754', '#ffc107', '#0dcaf0'],
        plotOptions: {
            pie: { donut: { size: '60%', labels: { show: true, name: { show: true }, value: { show: true } } } }
        },
        legend: { position: 'bottom' }
    }).render();

    // Gráfica de Barras - Embudo Agosto (Citas, Propuestas, Cierres)
    new ApexCharts(document.querySelector("#chart-funnel-julio"), {
        series: [{
            name: 'Total',
            data: <?php echo json_encode($julioFunnelValores); ?>
        }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#0d6efd', '#ffc107', '#198754'],
        plotOptions: {
            bar: { 
                distributed: true, 
                borderRadius: 5, 
                columnWidth: '40%',
                dataLabels: { position: 'top' } 
            }
        },
        dataLabels: { 
            enabled: true, 
            offsetY: -20, 
            style: { fontSize: '14px', fontWeight: 'bold', colors: ["#304758"] } 
        },
        xaxis: { categories: <?php echo json_encode($julioFunnelLabels); ?> },
        legend: { show: false }
    }).render();

    // =========================================================================
    // FIX DE REDIMENSIONAMIENTO PARA APEXCHARTS EN PESTAÑAS OCULTAS
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function() {
        var tabElements = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabElements.forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function (event) {
                window.dispatchEvent(new Event('resize'));
            });
        });
    });

</script>

<?= $this->endSection() ?>