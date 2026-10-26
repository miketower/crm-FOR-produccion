<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body py-3">
        <form action="<?= base_url('dashboard') ?>" method="get" id="filterForm" class="row g-3 align-items-end">
            
            <div class="col-md-3">
                <h4 class="fw-bold text-dark mb-0">Dashboard</h4>
                <small class="text-muted">Resumen de resultados</small>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Desde:</label>
                <input type="date" name="start" id="startDate" class="form-control form-control-sm" value="<?= $filtroInicio ?>">
            </div>
            
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Hasta:</label>
                <input type="date" name="end" id="endDate" class="form-control form-control-sm" value="<?= $filtroFin ?>">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar3"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="setRange('today')">Hoy</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setRange('this_week')">Esta Semana</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setRange('this_month')">Este Mes</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setRange('this_year')">Este Año</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setRange('last_year')">Año Pasado</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="setRange('all_time')">Histórico (Todo)</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4 border-top border-danger border-4">
    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
        <h5 class="fw-bold text-dark">
            <i class="bi bi-alarm text-danger me-2"></i>Mis Tareas Pendientes
        </h5>
    </div>
    <div class="card-body pt-2">
        
        <?php if(empty($tareasPendientes)): ?>
            <div class="text-center p-4 bg-light rounded">
                <i class="bi bi-cup-hot text-muted fs-1 mb-2"></i>
                <p class="text-muted mb-0 small">¡Todo al día! No tienes tareas pendientes por ahora.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach($tareasPendientes as $tarea): ?>
                    <div class="list-group-item px-0 py-3 border-bottom border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">
                                    <?= esc($tarea['company_name']) ?>
                                </h6>
                                <p class="mb-1 text-secondary small">
                                    <?= esc($tarea['subject']) ?>
                                </p>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">
                                    <i class="bi bi-calendar-event me-1"></i> 
                                    <?= date('d M Y, h:i a', strtotime($tarea['due_date'])) ?>
                                </span>
                            </div>
                            
                            <div class="ms-3">
                                <a href="<?= base_url('/prospectos/perfil/'.$tarea['prospect_id']) ?>" 
                                   class="btn btn-sm btn-outline-primary rounded-pill shadow-sm" 
                                   title="Ir al Perfil">
                                    Ver <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card shadow border-0 border-top border-primary border-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title fw-bold mb-0">Trayectoria a Meta 100 Millones (2026)</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Proyección de Venta Acumulada vs Crecimiento Mensual Real</small>
                </div>
                <div class="text-end">
                    <span class="badge <?= $diferenciaEnero >= 0 ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 <?= $diferenciaEnero >= 0 ? 'text-success' : 'text-danger' ?> border px-2 py-1 rounded-pill" style="font-size: 0.70rem;">
                        Estatus Enero: <?= $estatusEnero ?> (Brecha: $<?= number_format($diferenciaEnero, 2) ?>)
                    </span>
                </div>
            </div>
            
            <div class="card-body bg-light rounded m-3 p-2">
                <div class="row text-center g-2">
                    <div class="col-6 col-md-3 border-end border-secondary-subtle">
                        <small class="text-muted fw-bold text-uppercase d-block text-truncate" style="font-size: 0.65rem;">Venta Acumulada (2025)</small>
                        <h5 class="fw-bold text-dark mt-1 mb-0">$<?= number_format($ventaHeredada, 2) ?></h5>
                    </div>
                    <div class="col-6 col-md-3 border-end border-secondary-subtle">
                        <small class="text-muted fw-bold text-uppercase d-block text-truncate" style="font-size: 0.65rem;">Objetivo Crecimiento</small>
                        <h5 class="fw-bold text-primary mt-1 mb-0">$<?= number_format($diferenciaMeta, 2) ?></h5>
                    </div>
                    <div class="col-6 col-md-3 border-end border-secondary-subtle">
                        <small class="text-muted fw-bold text-uppercase d-block text-truncate" style="font-size: 0.65rem;">Cuota Mensual Fija</small>
                        <h5 class="fw-bold text-info mt-1 mb-0">$<?= number_format($metaMensual, 2) ?></h5>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted fw-bold text-uppercase d-block text-truncate" style="font-size: 0.65rem;">Enero (<?= $clientesEnero ?> Clientes)</small>
                        <h5 class="fw-bold <?= $ingresoEnero >= $metaMensual ? 'text-success' : 'text-danger' ?> mt-1 mb-0">
                            $<?= number_format($ingresoEnero, 2) ?>
                        </h5>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <div id="chartMeta100M" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow border-0 border-top border-success border-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="card-title fw-bold mb-0">Arranque de Año (Ene 25 vs Ene 26)</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Comparativa de Ingreso Nuevo y Acumulado</small>
            </div>
            
            <div class="card-body bg-light rounded m-3 p-2">
                <div class="row text-center g-2">
                    <div class="col-6 border-end border-secondary-subtle">
                        <small class="text-muted fw-bold text-uppercase d-block text-truncate" style="font-size: 0.65rem;">Ingreso Nuevo</small>
                        <h6 class="fw-bold text-success mt-1 mb-0">+231% <i class="bi bi-arrow-up-short"></i></h6>
                    </div>
                    <div class="col-6">
                        <small class="text-muted fw-bold text-uppercase d-block text-truncate" style="font-size: 0.65rem;">Acumulado Total</small>
                        <h6 class="fw-bold text-danger mt-1 mb-0">-11.8% <i class="bi bi-arrow-down-short"></i></h6>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <div id="chartComparativaEnero" style="min-height: 250px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-bold mb-1">Crecimiento (Vs. Periodo Anterior)</h6>
                    <div class="d-flex align-items-baseline gap-3">
                        <h2 class="mb-0 fw-bold text-dark">$<?= number_format($montoActual, 2) ?></h2>
                        
                        <?php if($diferenciaDinero >= 0): ?>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                <i class="bi bi-arrow-up-right"></i> +<?= number_format($porcentajeCrecimiento, 1) ?>%
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                <i class="bi bi-arrow-down-right"></i> <?= number_format($porcentajeCrecimiento, 1) ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-muted small mt-3 mb-0">
                        <span class="fw-bold">Diferencia:</span> 
                        <span class="<?= $diferenciaDinero >= 0 ? 'text-success' : 'text-danger' ?>">
                            $<?= number_format($diferenciaDinero, 2) ?>
                        </span> 
                        vs el año pasado ($<?= number_format($montoPrevio, 2) ?>)
                    </p>
                </div>
                
                <div class="d-none d-md-block text-muted opacity-25">
                    <i class="bi bi-graph-up display-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body position-relative text-center">
                <h6 class="text-muted text-uppercase fw-bold mb-0">Meta Anual de Ventas</h6>
                <div class="small fw-bold text-primary">Objetivo: $100,000,000</div>
                
                <div id="chartMeta" class="d-flex justify-content-center mt-3" style="min-height: 250px;"></div>

                <div class="mt-n4">
                    <p class="text-muted mb-1 small">Progreso Actual:</p>
                    <h3 class="fw-bold text-dark mb-0">$<?= number_format($ventaTotalAnual, 2) ?></h3>
                    
                    <div class="small mt-2 <?= $faltaParaMeta <= 0 ? 'text-success fw-bold' : 'text-danger' ?>">
                        <?php if($faltaParaMeta <= 0): ?>
                            ¡META SUPERADA!
                        <?php else: ?>
                            Faltan: $<?= number_format($faltaParaMeta, 2) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Pipeline Generado</div>
                <div class="fs-2 fw-bold text-dark mt-2">$<?= number_format($totalPipeline, 2) ?></div>
                <small class="text-primary">En el periodo seleccionado</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Ventas Cerradas</div>
                <div class="fs-2 fw-bold text-dark mt-2">$<?= number_format($totalGanado, 2) ?></div>
                <small class="text-success">Ingresos en este periodo</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Meta Ticket</div>
                <div class="fs-2 fw-bold text-dark mt-2">$1,500,000</div>
                <small class="text-muted">Objetivo estático</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    
    <div class="col-lg-6">
        <div class="card shadow border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-bold mb-0">Clientes Cerrados (<?= $anioPasado ?> vs <?= $anioActual ?>)</h5>
                <small class="text-muted">Cantidad de proyectos ganados por mes</small>
            </div>
            <div class="card-body">
                <div id="chartClientesYoY"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-bold mb-0">Ticket Promedio (<?= $anioPasado ?> vs <?= $anioActual ?>)</h5>
                <small class="text-muted">Comparativa de valor promedio de venta</small>
            </div>
            <div class="card-body">
                <div id="chartIngresoYoY"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos comunes (Meses)
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        // --- GRÁFICA 4: CLIENTES CERRADOS (Columnas Agrupadas) ---
        var optionsClientes = {
            series: [{
                name: '<?= $anioPasado ?>',
                data: <?= json_encode($clientesPasado) ?>
            }, {
                name: '<?= $anioActual ?>',
                data: <?= json_encode($clientesActual) ?>
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: { enabled: false },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: meses,
            },
            yaxis: {
                title: { text: 'Clientes Ganados' }
            },
            fill: { opacity: 1 },
            colors: ['#6c757d', '#0d6efd'], // Gris (Pasado) vs Azul (Actual)
            tooltip: {
                y: { formatter: function (val) { return val + " clientes" } }
            }
        };
        new ApexCharts(document.querySelector("#chartClientesYoY"), optionsClientes).render();


        // --- GRÁFICA 5: INGRESO PROMEDIO (Área o Línea para tendencia) ---
        // Usaremos "Area" para que se vea la diferencia de volumen visualmente
        var optionsIngreso = {
            series: [{
                name: '<?= $anioPasado ?>',
                data: <?= json_encode($ingresoPasado) ?>
            }, {
                name: '<?= $anioActual ?>',
                data: <?= json_encode($ingresoActual) ?>
            }],
            chart: {
                height: 350,
                type: 'area', // Tipo Área se ve muy bien para comparar volúmenes financieros
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: meses,
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return "$" + (value / 1000).toFixed(0) + "k";
                    }
                }
            },
            colors: ['#adb5bd', '#198754'], // Gris (Pasado) vs Verde (Actual - Dinero)
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3, // Degradado suave
                    stops: [0, 90, 100]
                }
            },
            tooltip: {
                y: { formatter: function (val) { return "$" + val.toLocaleString() } }
            }
        };
        new ApexCharts(document.querySelector("#chartIngresoYoY"), optionsIngreso).render();
    });
</script>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-bold mb-0">📊 Embudo (Periodo Seleccionado)</h5>
            </div>
            <div class="card-body">
                <div id="chartFunnel" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-bold mb-0">🎯 Calidad</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div id="chartDonut" class="w-100"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title fw-bold mb-0">💰 Top Servicios (Periodo Seleccionado)</h5>
            </div>
            <div class="card-body">
                <div id="chartServices" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DE FECHAS RÁPIDAS ---
    function setRange(type) {
        const today = new Date();
        let start = new Date();
        let end = new Date(); // Por defecto es hoy

        if (type === 'today') {
            // Start y End son hoy
        } else if (type === 'this_week') {
            const day = today.getDay(); 
            const diff = today.getDate() - day + (day == 0 ? -6 : 1); 
            start.setDate(diff);
        } else if (type === 'this_month') {
            start = new Date(today.getFullYear(), today.getMonth(), 1);
        } else if (type === 'this_year') {
            start = new Date(today.getFullYear(), 0, 1);
        } 
        // --- LÓGICA NUEVA PARA AÑO PASADO ---
        else if (type === 'last_year') {
            start = new Date(today.getFullYear() - 1, 0, 1);   // 1 Ene Año Pasado
            end = new Date(today.getFullYear() - 1, 11, 31);   // 31 Dic Año Pasado
        } 
        // -------------------------------------
        else if (type === 'all_time') {
            start = new Date(2020, 0, 1);
        }

        // Formatear a YYYY-MM-DD
        const formatDate = (d) => {
            let month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }

        document.getElementById('startDate').value = formatDate(start);
        document.getElementById('endDate').value = formatDate(end);
        
        document.getElementById('filterForm').submit();
    }

    // --- GRÁFICAS (APEXCHARTS) ---
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Embudo
        const embudoLabels = []; const embudoSeries = [];
        <?php foreach($embudoData as $row): ?>
            embudoLabels.push('<?= $row['pipeline_stage'] ?>');
            embudoSeries.push(<?= $row['total'] ?>);
        <?php endforeach; ?>

        var optionsFunnel = {
            series: [{ name: 'Prospectos', data: embudoSeries }],
            chart: { type: 'bar', height: 350 },
            plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true } },
            xaxis: { categories: embudoLabels },
            colors: ['#6f42c1', '#0d6efd', '#0dcaf0', '#ffc107', '#198754', '#dc3545', '#6c757d']
        };
        new ApexCharts(document.querySelector("#chartFunnel"), optionsFunnel).render();

        // 2. Donut (Grupos)
        const gruposLabels = []; const gruposSeries = []; const groupColors = [];
        <?php foreach($gruposData as $row): ?>
            gruposLabels.push('Grupo <?= $row['classification_group'] ?>');
            gruposSeries.push(<?= $row['total'] ?>);
            <?php 
                $g = $row['classification_group'];
                if($g == 'A') echo "groupColors.push('#198754');";
                elseif($g == 'B') echo "groupColors.push('#0d6efd');";
                elseif($g == 'C') echo "groupColors.push('#ffc107');";
                else echo "groupColors.push('#dc3545');";
            ?>
        <?php endforeach; ?>

        if(gruposSeries.length > 0) {
            var optionsDonut = {
                series: gruposSeries, labels: gruposLabels, chart: { type: 'donut', height: 350 },
                colors: groupColors,
                plotOptions: { pie: { donut: { size: '65%' } } }
            };
            new ApexCharts(document.querySelector("#chartDonut"), optionsDonut).render();
        } else {
            document.querySelector("#chartDonut").innerHTML = "<div class='text-center text-muted py-5'>Sin datos en este periodo.</div>";
        }

        // 3. Servicios
        const servLabels = []; const servSeries = [];
        <?php foreach($serviciosData as $row): ?>
            servLabels.push('<?= $row['name'] ?>');
            servSeries.push(<?= $row['total_dinero'] ?>);
        <?php endforeach; ?>

        var optionsServ = {
            series: [{ name: 'Ingresos ($)', data: servSeries }],
            chart: { type: 'bar', height: 300 },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
            xaxis: { categories: servLabels },
            yaxis: { labels: { formatter: function (val) { return "$" + (val / 1000).toFixed(0) + "k"; } } },
            fill: { colors: ['#20c997'] }
        };
        new ApexCharts(document.querySelector("#chartServices"), optionsServ).render();

        // 4. CHART META ANUAL (Radial Bar - 100M)
        var optionsMeta = {
            series: [<?= round($porcentajeGrafica, 1) ?>], 
            chart: {
                height: 300,
                type: 'radialBar',
                offsetY: -20
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    hollow: {
                        margin: 15,
                        size: '65%',
                        background: 'transparent',
                    },
                    track: {
                        background: '#f8f9fa',
                        strokeWidth: '100%',
                    },
                    dataLabels: {
                        show: true,
                        name: {
                            show: false, // Ocultamos el texto "Series 1"
                        },
                        value: {
                            offsetY: 10,
                            color: '#111',
                            fontSize: '30px',
                            fontWeight: 'bold',
                            show: true,
                            formatter: function (val) {
                                return val + "%";
                            }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    shadeIntensity: 0.5,
                    // Degradado: Empieza en Rojo/Naranja y termina en Verde Brillante
                    gradientToColors: ['#00E396'], 
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                }
            },
            stroke: {
                lineCap: 'round'
            },
            colors: ['#FF4560'], // Color inicial (rojo cuando está bajo)
            labels: ['Progreso'],
        };

        var chartMeta = new ApexCharts(document.querySelector("#chartMeta"), optionsMeta);
        chartMeta.render();
    });

    // --- GRÁFICA: TRAYECTORIA 100 MILLONES ---
    document.addEventListener('DOMContentLoaded', function() {
        var optionsMeta100M = {
            series: [
                {
                    name: 'Venta Real Acumulada',
                    type: 'area',
                    data: <?= json_encode($ventaRealAcumulada) ?>
                },
                {
                    name: 'Trayectoria Meta (100M)',
                    type: 'line',
                    data: <?= json_encode($trayectoriaMeta) ?>
                }
            ],
            chart: {
                height: 420,
                type: 'line',
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            stroke: {
                curve: 'smooth',
                width: [4, 3],
                dashArray: [0, 6] // Línea continua para Real, punteada para Meta
            },
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            colors: ['#0d6efd', '#6c757d'], // Azul para lo real, Gris para la meta
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            markers: {
                size: [6, 0], // Solo poner puntitos en la venta real
                colors: ['#fff'],
                strokeColors: '#0d6efd',
                strokeWidth: 3
            },
            yaxis: {
                min: 50000000, // Empezamos la gráfica cerca de los 55M para que los saltos se vean bien
                max: 105000000,
                labels: {
                    formatter: function (value) {
                        return "$" + (value / 1000000).toFixed(1) + "M";
                    }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y) {
                        if (typeof y !== "undefined" && y !== null) {
                            return "$" + y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                        return "Sin datos";
                    }
                }
            },
            annotations: {
                yAxis: [
                    {
                        y: <?= $ventaHeredada ?>,
                        borderColor: '#adb5bd',
                        label: {
                            borderColor: '#adb5bd',
                            style: { color: '#fff', background: '#adb5bd', fontSize: '12px' },
                            text: 'Inicio 2026: $55.2M'
                        }
                    },
                    {
                        y: 100000000,
                        borderColor: '#198754',
                        label: {
                            borderColor: '#198754',
                            style: { color: '#fff', background: '#198754', fontSize: '12px', fontWeight: 'bold' },
                            text: '🎯 META: $100M'
                        }
                    }
                ]
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4,
            }
        };
        new ApexCharts(document.querySelector("#chartMeta100M"), optionsMeta100M).render();
    });

    // --- GRÁFICA: COMPARATIVA ENERO 25 VS 26 (AGRUPADA) ---
    var optionsComparativa = {
        series: [
            {
                name: '2025',
                data: [772087.50, 5054416.75] // Ingreso Nuevo 25, Acumulado 25
            },
            {
                name: '2026',
                data: [2555443.73, 4456396.16] // Ingreso Nuevo 26, Acumulado 26
            }
        ],
        chart: {
            type: 'bar',
            height: 280,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                columnWidth: '60%',
                dataLabels: {
                    position: 'top', // Etiquetas arriba de cada barra
                },
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return "$" + (val / 1000000).toFixed(2) + "M"; // Formato en Millones
            },
            offsetY: -20,
            style: {
                fontSize: '10px',
                colors: ["#304758"]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent'] // Pequeña separación entre las barras agrupadas
        },
        colors: ['#adb5bd', '#0d6efd'], // Gris para 2025, Azul corporativo para 2026
        xaxis: {
            categories: ['Ingreso Nuevo', 'Acumulado Total'],
            labels: {
                style: { fontWeight: 'bold', fontSize: '11px' }
            }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "$" + (val / 1000000).toFixed(1) + "M";
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "$" + val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            fontSize: '12px',
            markers: { radius: 12 }
        }
    };

    var chartEnero = new ApexCharts(document.querySelector("#chartComparativaEnero"), optionsComparativa);
    chartEnero.render();
</script>

<?= $this->endSection() ?>