<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark"><?php echo $titulo ?? 'Carga Semanal de Ingresos'; ?></h4>
        <small class="text-muted">Ingresa los totales acumulados de la semana en curso.</small>
    </div>
    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="bi bi-arrow-left"></i> Volver al Dashboard
    </a>
</div>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm border-0 border-start border-4 border-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm border-0 border-start border-4 border-success">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= base_url('cortes-semanales/guardar') ?>" method="POST">
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small text-uppercase">Fecha Inicio de Semana</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small text-uppercase">Fecha Fin de Semana</label>
                    <input type="date" name="fecha_fin" class="form-control" required>
                    <small class="text-muted" style="font-size: 11px;">Define el "Periodo" en el Dashboard.</small>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th class="text-start text-muted small" style="width: 20%;">ESQUEMA SERVICIO</th>
                            <th class="text-muted small" style="width: 20%;">TIPO DE VENTA</th>
                            <th class="text-muted small">FACTURACIÓN</th>
                            <th class="text-muted small">IB NUEVO</th>
                            <th class="text-muted small">IB TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($esquemasServicio as $index => $esquema): ?>
                        <tr>
                            <td class="fw-bold text-secondary">
                                <?= $esquema['nombre'] ?>
                                <input type="hidden" name="servicios[<?= $index ?>][cat_esquema_servicio_id]" value="<?= $esquema['id'] ?>">
                            </td>
                            <td>
                                <select name="servicios[<?= $index ?>][cat_tipo_venta_id]" class="form-select border-0 bg-light" required>
                                    <option value="">Seleccione canal...</option>
                                    <?php foreach($tiposVenta as $tv): ?>
                                        <option value="<?= $tv['id'] ?>"><?= $tv['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">$</span>
                                    <input type="number" step="0.01" min="0" name="servicios[<?= $index ?>][facturacion]" class="form-control border-start-0 calc-fac" placeholder="0.00" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">$</span>
                                    <input type="number" step="0.01" min="0" name="servicios[<?= $index ?>][ib_nuevo]" class="form-control border-start-0 calc-ibn" placeholder="0.00" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">$</span>
                                    <input type="number" step="0.01" min="0" name="servicios[<?= $index ?>][ib_total]" class="form-control border-start-0 calc-ibt" placeholder="0.00" required>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end text-muted">TOTALES:</td>
                            <td class="text-center text-primary fs-5" id="total-fac">$0.00</td>
                            <td class="text-center text-success fs-5" id="total-ibn">$0.00</td>
                            <td class="text-center text-dark fs-5" id="total-ibt">$0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-cloud-arrow-up-fill me-2"></i>Guardar Avance Semanal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formatMoney = (amount) => {
            return '$' + parseFloat(amount).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const calculateTotals = () => {
            let totalFac = 0, totalIbn = 0, totalIbt = 0;
            document.querySelectorAll('.calc-fac').forEach(input => totalFac += parseFloat(input.value || 0));
            document.querySelectorAll('.calc-ibn').forEach(input => totalIbn += parseFloat(input.value || 0));
            document.querySelectorAll('.calc-ibt').forEach(input => totalIbt += parseFloat(input.value || 0));

            document.getElementById('total-fac').innerText = formatMoney(totalFac);
            document.getElementById('total-ibn').innerText = formatMoney(totalIbn);
            document.getElementById('total-ibt').innerText = formatMoney(totalIbt);
        };

        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
    });
</script>

<?= $this->endSection() ?>