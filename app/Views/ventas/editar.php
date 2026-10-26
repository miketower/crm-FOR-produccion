<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="card shadow border-0 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4 text-dark">Modificar Flujo / Registrar Pago</h5>
        
        <form action="<?= base_url('ventas/actualizar/' . $venta['id']) ?>" method="POST">
            <div class="row">
                <!-- Cliente -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold small text-muted">Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <?php foreach($clientes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($venta['cliente_id'] == $c['id']) ? 'selected' : '' ?>><?= esc($c['razon_social']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Tipo de Venta -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold small text-muted">Tipo de Venta</label>
                    <select name="cat_tipo_venta_id" class="form-select" required>
                        <?php foreach($tipos_ventas as $tv): ?>
                            <option value="<?= $tv['id'] ?>" <?= ($venta['cat_tipo_venta_id'] == $tv['id']) ? 'selected' : '' ?>><?= esc($tv['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Esquema de Servicio -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold small text-muted">Esquema de Servicio</label>
                    <select name="cat_esquema_servicio_id" class="form-select" required>
                        <?php foreach($esquemas as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= ($venta['cat_esquema_servicio_id'] == $e['id']) ? 'selected' : '' ?>><?= esc($e['nombre']) ?> (<?= esc($e['abreviatura']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold small text-muted">Comisión Bruta ($)</label>
                    <input type="number" step="0.01" name="comision_bruta" class="form-control" value="<?= esc($venta['comision_bruta']) ?>">
                </div>
            </div>

            <hr class="my-4 text-muted">

            <div class="row">
                <!-- Sección: Cierre Comercial (Vendido) -->
                <div class="col-md-6 mb-3 border-end">
                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-file-earmark-check"></i> Datos de Cierre (Vendido)</h6>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Monto Vendido ($)</label>
                        <input type="number" step="0.01" name="monto_vendido" class="form-control" value="<?= esc($venta['monto_vendido']) ?>" required>
                    </div>
                    <div>
                        <label class="form-label small text-muted">Fecha de Vendido</label>
                        <input type="date" name="fecha_vendido" class="form-control" value="<?= esc($venta['fecha_vendido']) ?>" required>
                    </div>
                </div>

                <!-- Sección: Flujo de Caja (Cobrado) -->
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-cash-coin"></i> Flujo de Caja (Marcar como Cobrado)</h6>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Monto Cobrado ($)</label>
                        <input type="number" step="0.01" name="monto_cobrado" class="form-control" value="<?= esc($venta['monto_cobrado']) ?>">
                    </div>
                    <div>
                        <label class="form-label small text-muted">Fecha de Cobrado</label>
                        <input type="date" name="fecha_cobrado" class="form-control" value="<?= esc($venta['fecha_cobrado']) ?>">
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="<?= base_url('ventas') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>