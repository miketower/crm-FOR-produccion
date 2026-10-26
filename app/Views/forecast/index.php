<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-dark"><?= $titulo ?></h4>
    <div>
        <label class="fw-bold text-muted me-2">Año Fiscal:</label>
        <select id="select-anio" class="form-select form-select-sm d-inline-block w-auto shadow-sm">
            <?php 
                $anioActual = date('Y');
                for($i = $anioActual - 1; $i <= $anioActual + 3; $i++): 
            ?>
                <option value="<?= $i ?>" <?= $i == $anioActual ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<form id="form-forecast">
    <input type="hidden" name="anio" id="input-anio" value="<?= $anioActual ?>">
    
    <!-- Bloque 1: Meta Anual -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-globe me-2"></i>Meta Global Anual</h6>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Facturación Objetivo ($)</label>
                    <input type="text" class="form-control form-control-lg text-success fw-bold number-format" name="meta_anual" id="meta_anual" value="0.00">
                </div>
            </div>
        </div>
    </div>

    <!-- Bloque 2: Proyección por Q -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-pie-chart me-2"></i>Ingreso Bruto Proyectado por Trimestre (Q)</h6>
            <div class="row g-3">
                <?php for($q = 1; $q <= 4; $q++): ?>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Q<?= $q ?></label>
                    <input type="text" class="form-control number-format" name="q<?= $q ?>_ingreso" id="q<?= $q ?>_ingreso" value="0.00">
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Bloque 3: Proyección Operativa Mensual -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-check me-2"></i>Actividades Comerciales por Mes</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mes</th>
                            <th>Citas</th>
                            <th>Propuestas</th>
                            <th>Cierres</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        foreach($meses as $idx => $mes): 
                            $m = $idx + 1;
                        ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $mes ?></td>
                            <td><input type="number" class="form-control text-center" name="m<?= $m ?>_citas" id="m<?= $m ?>_citas" value="0" min="0"></td>
                            <td><input type="number" class="form-control text-center" name="m<?= $m ?>_propuestas" id="m<?= $m ?>_propuestas" value="0" min="0"></td>
                            <td><input type="number" class="form-control text-center text-success fw-bold" name="m<?= $m ?>_cierres" id="m<?= $m ?>_cierres" value="0" min="0"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-end mb-5">
        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btn-guardar">
            <i class="bi bi-save me-2"></i>Guardar Proyecciones
        </button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    
    // Formateador simple de números (añade comas)
    $('.number-format').on('keyup', function() {
        let val = $(this).val().replace(/,/g, '').replace(/[^0-9.]/g, '');
        if(val !== '') {
            let parts = val.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $(this).val(parts.join("."));
        }
    });

    // Función para cargar datos por AJAX
    function loadForecastData(anio) {
        $('#input-anio').val(anio);
        
        $.get('<?= base_url('forecast/getYearData') ?>/' + anio, function(data) {
            // Limpiar formulario primero
            $('#form-forecast').trigger('reset');
            $('#input-anio').val(anio);

            // 1. Cargar Anual
            if(data.anual) {
                $('#meta_anual').val(parseFloat(data.anual.meta_anual).toLocaleString('en-US', {minimumFractionDigits: 2}));
            } else {
                $('#meta_anual').val('0.00');
            }

            // 2. Cargar Trimestral
            if(data.trimestral.length > 0) {
                data.trimestral.forEach(function(item) {
                    $('#q' + item.q + '_ingreso').val(parseFloat(item.ingreso_bruto).toLocaleString('en-US', {minimumFractionDigits: 2}));
                });
            } else {
                for(let i=1; i<=4; i++) $('#q' + i + '_ingreso').val('0.00');
            }

            // 3. Cargar Mensual
            if(data.mensual.length > 0) {
                data.mensual.forEach(function(item) {
                    $('#m' + item.mes + '_citas').val(item.citas);
                    $('#m' + item.mes + '_propuestas').val(item.propuestas);
                    $('#m' + item.mes + '_cierres').val(item.cierres);
                });
            } else {
                for(let i=1; i<=12; i++) {
                    $('#m' + i + '_citas').val('0');
                    $('#m' + i + '_propuestas').val('0');
                    $('#m' + i + '_cierres').val('0');
                }
            }
        }, 'json');
    }

    // Cargar datos al iniciar con el año actual
    loadForecastData($('#select-anio').val());

    // Cargar datos al cambiar el select
    $('#select-anio').on('change', function() {
        loadForecastData($(this).val());
    });

    // Guardar formulario
    $('#form-forecast').on('submit', function(e) {
        e.preventDefault();
        
        let formData = $(this).serialize();
        let btn = $('#btn-guardar');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');

        $.post('<?= base_url('forecast/save') ?>', formData, function(response) {
            btn.prop('disabled', false).html('<i class="bi bi-save me-2"></i>Guardar Proyecciones');
            
            if(response.status === 'success') {
                Swal.fire('¡Éxito!', response.message, 'success');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }, 'json').fail(function() {
            btn.prop('disabled', false).html('<i class="bi bi-save me-2"></i>Guardar Proyecciones');
            Swal.fire('Error', 'Hubo un error de comunicación con el servidor.', 'error');
        });
    });

});
</script>

<?= $this->endSection() ?>