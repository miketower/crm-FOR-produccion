<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/locales.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?= $titulo ?></h2>
        <small class="text-muted">Prospectos creados en el periodo seleccionado</small>
    </div>
    
    <div class="input-group shadow-sm" style="width: auto;">
        <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-calendar3"></i></span>
        <input type="text" id="rango_fechas_pipeline" class="form-control border-start-0 fw-bold text-muted" style="cursor: pointer; width: 250px;" readonly>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 bg-white rounded border-start border-primary border-4">
            <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Valor Total Pipeline (Año <?= date('Y', strtotime($end)) ?>)</small>
            <h3 class="fw-bold mb-0 text-dark">$<?= number_format($totalAnualPipeline, 2); ?></h3>
        </div>
    </div>
</div>

<div class="kanban-board d-flex gap-3" style="overflow-x: auto; min-height: 70vh;">
    <?php 
    $etapas = ['Nuevo', 'Contactado', 'Propuesta', 'Cerrado Ganado']; 
    
    foreach ($etapas as $etapa): 
        // [NUEVO] LÓGICA DE SUMATORIA POR COLUMNA
        $sumaColumna = 0;
        foreach ($prospectos as $p) {
            if (trim($p['pipeline_stage']) == $etapa) {
                $sumaColumna += (float)($p['ticket_value'] ?? 0);
            }
        }
    ?>
        <div class="kanban-col bg-light border rounded p-2" style="min-width: 280px; width: 280px;">
            <div class="px-2 py-2 border-bottom text-center">
                <h6 class="fw-bold text-secondary text-uppercase mb-0"><?= $etapa ?></h6>
                <small class="text-primary fw-bold" style="font-size: 12px;">$<?= number_format($sumaColumna, 2) ?></small>
            </div>
            
            <div class="cards-container p-2" 
                id="col-<?= strtolower(str_replace(' ', '-', $etapa)) ?>" 
                data-stage="<?= $etapa ?>"
                style="min-height: 200px;"> <?php foreach ($prospectos as $p): ?>
                    <?php if (trim($p['pipeline_stage']) == $etapa): 
                        $borderClass = 'border-secondary';
                        if ($etapa == 'Contactado') $borderClass = 'border-primary';
                        if ($etapa == 'Propuesta')  $borderClass = 'border-warning';
                        if ($etapa == 'Cerrado Ganado') $borderClass = 'border-success';
                    ?>
                        <div class="kanban-card bg-white p-3 mb-2 shadow-sm rounded border-start <?= $borderClass ?> border-4"
                            data-id="<?= $p['id'] ?>">
                            <div class="fw-bold text-dark text-truncate"><?= esc($p['company_name']) ?></div>
                            <div class="text-muted small">Valor: $<?= number_format($p['ticket_value'] ?? 0, 2) ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
$(document).ready(function() {
    moment.locale('es');
    
    var start = moment('<?= $start; ?>');
    var end = moment('<?= $end; ?>');

    function actualizarTexto(start, end) {
        $('#rango_fechas_pipeline').val(start.format('D MMM, YYYY') + ' - ' + end.format('D MMM, YYYY'));
    }

    $('#rango_fechas_pipeline').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Este Mes': [moment().startOf('month'), moment().endOf('month')],
           'Mes Anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Año Actual (YTD)': [moment().startOf('year'), moment().endOf('year')],
           'Año Pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
           'Q1': [moment().startOf('year'), moment().startOf('year').add(2, 'months').endOf('month')],
           'Q2': [moment().startOf('year').add(3, 'months'), moment().startOf('year').add(5, 'months').endOf('month')],
           'Q3': [moment().startOf('year').add(6, 'months'), moment().startOf('year').add(8, 'months').endOf('month')],
           'Q4': [moment().startOf('year').add(9, 'months'), moment().endOf('year')]
        },
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            customRangeLabel: "Rango Libre"
        }
    }, function(start, end) {
        window.location.href = "?start=" + start.format('YYYY-MM-DD') + "&end=" + end.format('YYYY-MM-DD');
    });

    actualizarTexto(start, end);
});

// Inicialización de Sortable (Mantiene tu lógica drag & drop intacta)
document.querySelectorAll('.cards-container').forEach(container => {
    new Sortable(container, {
        group: 'pipeline',
        animation: 150,
        onEnd: function (evt) {
            const itemId = evt.item.getAttribute('data-id');
            const newStage = evt.to.getAttribute('data-stage');

            fetch('<?= base_url('dashboard/updateStage') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `id=${itemId}&stage=${newStage}`
            })
            .then(response => response.json())
            .then(data => console.log(data.message));
        }
    });
});
</script>

<?= $this->endSection() ?>