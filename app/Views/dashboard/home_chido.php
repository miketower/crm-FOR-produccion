<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- Agrega esto en tu cabecera o al principio de la vista -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<!-- Agregamos moment con español para que los nombres de los meses salgan en español -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/locales.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />



<!-- (Aquí va todo tu HTML de tarjetas y gráficas, que queda exactamente igual) -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark"><?php echo $titulo; ?></h4>
        <small class="text-muted">Periodo: <?php echo $filtroInicio; ?> - <?php echo $filtroFin; ?></small>
    </div>

    <div class="input-group shadow-sm" style="width: auto;">
        <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-calendar3"></i></span>
        <input type="text" id="rango_fechas" class="form-control border-start-0 fw-bold text-muted" style="cursor: pointer; width: 250px;" readonly>
    </div>
    
</div>

<div class="row g-3 mb-4 text-center">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3" style="border-top: 4px solid #198754;">
            <small class="text-muted fw-bold small text-uppercase">Facturación</small>
            <h3 class="fw-bold mb-0 text-success">$<?php echo number_format($totalValidadoTotal, 2); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3" style="border-top: 4px solid #0d6efd;">
            <small class="text-muted fw-bold small text-uppercase">Ingreso Bruto Total</small>
            <h3 class="fw-bold mb-0 text-primary">$<?php echo number_format($ingresoBrutoTotal, 2); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3" style="border-top: 4px solid #ffc107;">
            <small class="text-muted fw-bold small text-uppercase">Citas</small>
            <h3 class="fw-bold mb-0"><?php echo array_sum($dataCitas); ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3" style="border-top: 4px solid #6f42c1;">
            <small class="text-muted fw-bold small text-uppercase">Cierres</small>
            <h3 class="fw-bold mb-0"><?php echo array_sum($dataCierres); ?></h3>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-bar-chart-fill me-2"></i>Desempeño Financiero</h5>
                <div id="chart-montos-vs"></div>
                
                <hr class="my-4">
                
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="small fw-bold text-muted">Ingreso Bruto por Canal</h6>
                        <div id="pie-vendido"></div>
                    </div>
                    <div class="col-6">
                        <h6 class="small fw-bold text-muted">Facturado por Canal</h6>
                        <div id="pie-cobrado"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
            <h6 class="fw-bold text-muted text-uppercase small text-center mb-3">Avance Objetivo: $<?php echo number_format($metaAnual ?? 0, 0); ?></h6>
                <div class="row">
                    <!-- Dona del Periodo Actual (Trimestre/Mes) -->
                    <div class="col-6 text-center">
                        <small class="text-muted d-block small">Avance Periodo</small>
                        <div id="chart-meta-periodo"></div>
                    </div>
                    <!-- Dona del Acumulado Anual (YTD) -->
                    <div class="col-6 text-center">
                        <small class="text-muted d-block small">Acumulado Anual</small>
                        <div id="chart-meta-anual-ytd"></div>
                    </div>
                </div>
                <hr>
                <div class="mt-3">
                    <?php 
                    $coloresCanal = array('text-primary', 'text-success', 'text-warning');
                    foreach ($canalesLabels as $i => $label): 
                        // Leemos de la nueva variable de Ingreso Bruto
                        $monto = $dataIngresoBrutoCanal[$i];
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

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-4 text-success text-center"><i class="bi bi-percent me-2"></i>Efectividad (Cierres / Propuestas)</h5>
        <div id="chart-efectividad"></div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body">
        <h6 class="fw-bold mb-4 text-muted">Cumplimiento de Metas: Real vs Proyectado</h6>
        <div id="chart-vs-actividad"></div>
    </div>
</div>

<div class="row g-3 mb-4 text-center">
    <?php 
    $titulos = ['Citas', 'Propuestas', 'Cierres'];
    $ids     = ['#dona-citas', '#dona-propuestas', '#dona-cierres'];
    $data    = [$dataCitas, $dataPropuestas, $dataCierres];
    ?>
    <?php foreach ($titulos as $i => $titulo): ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold text-muted small"><?= $titulo ?> por Canal</h6>
            <div id="<?= str_replace('#', '', $ids[$i]) ?>"></div>
            
            <div class="mt-2 small border-top pt-2">
                <span class="text-muted">Efectividad Histórica:</span>
                <span class="fw-bold text-primary"><?= $dataEfectividad[$i] ?? 0 ?>%</span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
    const labels = <?php echo json_encode($canalesLabels); ?>;
    const coloresCanales = ['#0d6efd', '#198754', '#ffc107'];
    const moneyFormat = (v) => "$" + v.toLocaleString('es-MX');

    $(document).ready(function() {
        moment.locale('es'); // Fechas en español
        
        // Recibimos las fechas exactas de PHP para mantener el estado del filtro
        var start = moment('<?php echo $start; ?>');
        var end = moment('<?php echo $end; ?>');

        function actualizarTexto(start, end) {
            $('#rango_fechas').val(start.format('D MMM, YYYY') + ' - ' + end.format('D MMM, YYYY'));
        }

        $('#rango_fechas').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
               'Este Mes': [moment().startOf('month'), moment().endOf('month')],
               'Mes Anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'Q1 (Ene - Mar)': [moment().startOf('year'), moment().startOf('year').add(2, 'months').endOf('month')],
               'Q2 (Abr - Jun)': [moment().startOf('year').add(3, 'months'), moment().startOf('year').add(5, 'months').endOf('month')],
               'Q3 (Jul - Sep)': [moment().startOf('year').add(6, 'months'), moment().startOf('year').add(8, 'months').endOf('month')],
               'Q4 (Oct - Dic)': [moment().startOf('year').add(9, 'months'), moment().endOf('year')],
               'Año Actual (YTD)': [moment().startOf('year'), moment()]
            },
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: "Aplicar",
                cancelLabel: "Cancelar",
                customRangeLabel: "Rango Libre"
            }
        }, function(start, end, label) {
            // Al aplicar un filtro, recargamos la página pasando las fechas por GET
            window.location.href = "?start=" + start.format('YYYY-MM-DD') + "&end=" + end.format('YYYY-MM-DD');
        });

        // Inicializamos el input con las fechas actuales
        actualizarTexto(start, end);
    });

    // 1. Barras Financieras (Con formateador inteligente para Ingresos Nuevos)
    new ApexCharts(document.querySelector("#chart-montos-vs"), {
        series: [{ name: 'Monto', data: <?php echo json_encode($dataMontosVs); ?> }],
        chart: { type: 'bar', height: 320, toolbar: {show:false} },
        colors: ['#0d6efd', '#198754', '#6f42c1', '#adb5bd'],
        plotOptions: { bar: { distributed: true, borderRadius: 5, columnWidth: '55%' } },
        // AQUI ACTUALIZAMOS LOS NOMBRES DE LAS BARRAS
        xaxis: { categories: ['Ingreso Bruto', 'Facturado', 'Ing. Nuevo', 'Proyectado'] },
        yaxis: { 
            labels: { 
                formatter: function (v) {
                    if (v >= 1000000) return "$" + (v / 1000000).toFixed(1) + "M";
                    if (v > 0) return moneyFormat(v);
                    return "$0";
                }
            }
        },
        tooltip: { y: { formatter: moneyFormat } }
    }).render();

    // 2. Pies Financieros
    const pieCfg = (data) => ({
        series: data, chart: { type: 'pie', height: 200 },
        labels: labels, colors: coloresCanales, legend: { position: 'bottom' },
        tooltip: { y: { formatter: moneyFormat } }
    });
    // LE PASAMOS LAS NUEVAS VARIABLES DE PHP A JAVASCRIPT
    new ApexCharts(document.querySelector("#pie-vendido"), pieCfg(<?php echo json_encode($dataIngresoBrutoCanal); ?>)).render();
    new ApexCharts(document.querySelector("#pie-cobrado"), pieCfg(<?php echo json_encode($dataTotalValidadoCanal); ?>)).render();

    // 4. Efectividad
    new ApexCharts(document.querySelector("#chart-efectividad"), {
        series: [{ name: 'Efectividad', data: <?php echo json_encode($dataEfectividad); ?> }],
        chart: { type: 'bar', height: 250, toolbar: {show:false} },
        plotOptions: { bar: { distributed: true, borderRadius: 4, dataLabels: { position: 'top' } } },
        colors: coloresCanales, xaxis: { categories: labels },
        dataLabels: { enabled: true, formatter: (v) => v + "%", offsetY: -20 }
    }).render();

    // 5. Las 3 Donas de Actividad (Citas, Propuestas, Cierres)
    const donaCfg = (data, elementId) => {
        const sum = data.reduce((a, b) => a + b, 0);
        new ApexCharts(document.querySelector(elementId), {
            series: sum > 0 ? data : [], 
            chart: { type: 'donut', height: 200 },
            labels: labels, 
            colors: coloresCanales, 
            legend: { position: 'bottom' },
            noData: { text: 'Sin actividad registrada', align: 'center', verticalAlign: 'middle' }
        }).render();
    };
    donaCfg(<?php echo json_encode($dataCitas); ?>, "#dona-citas");
    donaCfg(<?php echo json_encode($dataPropuestas); ?>, "#dona-propuestas");
    donaCfg(<?php echo json_encode($dataCierres); ?>, "#dona-cierres");

    // 6. Cumplimiento de Metas: Real vs Proyectado
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

    // 7. Gráficas Radiales de Avance (Objetivo 100M)
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
    renderRadial(<?php echo $pctAcumulado; ?>, "#chart-meta-anual-ytd", '#6f42c1');
</script>

<?= $this->endSection() ?>