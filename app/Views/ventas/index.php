<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">Control de Ventas e Ingresos</h4>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVenta">
        <i class="bi bi-plus-circle"></i> Registrar Venta / Flujo
    </button>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<div class="card shadow border-0 mb-4">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="ps-4">Cliente</th>
                <th>Tipo</th>
                <th>Esquema</th>
                <th>Monto Vendido</th>
                <th>Fecha Vendido</th>
                <th>Monto Cobrado</th>
                <th>Estatus / Fecha</th>
                <th class="text-end pe-4">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($ventas)): ?>
                <?php foreach($ventas as $v): ?>
                <tr>
                    <!-- Cliente -->
                    <td class="ps-4 fw-bold text-dark"><?= esc($v['cliente_nombre']) ?></td>
                    
                    <!-- Tipo de Venta -->
                    <td><span class="badge bg-secondary text-white"><?= esc($v['tipo_venta'] ?? 'Otros') ?></span></td>
                    
                    <!-- Esquema -->
                    <td class="fw-bold text-primary"><?= esc($v['esquema']) ?></td>
                    
                    <!-- Monto Vendido -->
                    <td class="text-success fw-bold">$<?= number_format($v['monto_vendido'], 2) ?></td>
                    
                    <!-- Fecha Vendido -->
                    <td class="text-muted small"><?= $v['fecha_vendido'] ? date('d/m/Y', strtotime($v['fecha_vendido'])) : 'N/A' ?></td>
                    
                    <!-- Monto Cobrado -->
                    <td class="text-info fw-bold">
                        $<?= number_format($v['monto_cobrado'], 2) ?>
                    </td>
                    
                    <!-- Estatus / Fecha Cobrado -->
                    <td>
                        <?php if(!empty($v['fecha_cobrado']) && $v['monto_cobrado'] > 0): ?>
                            <span class="badge bg-success mb-1 d-block text-center" style="max-width: 110px;">Cobrado</span>
                            <span class="text-muted small d-block"><?= date('d/m/Y', strtotime($v['fecha_cobrado'])) ?></span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark d-block text-center" style="max-width: 110px;">Pendiente</span>
                        <?php endif; ?>
                    </td>

                    <!-- Botón de Acción (Editar / Registrar Pago) -->
                    <td class="text-end pe-4">
                        <a href="<?= base_url('ventas/editar/' . $v['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar / Registrar Pago">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center p-4 text-muted">No hay flujos de venta registrados aún.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <div class="text-muted small">Mostrando registros de flujos financieros</div>
        <div><?= $pager->links('default', 'bootstrap_full', 1) ?></div>
    </div>
</div>

<div class="modal fade" id="modalVenta" tabindex="-1" aria-labelledby="modalVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="modalVentaLabel">Registrar Nuevo Flujo de Ingreso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('ventas/guardar') ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Cliente</label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">-- Seleccionar Cliente --</option>
                                <?php foreach($clientes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= esc($c['razon_social']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Tipo de Venta</label>
                            <select name="cat_tipo_venta_id" class="form-select" required>
                                <option value="">-- Seleccionar Tipo --</option>
                                <?php foreach($tipos_ventas as $tv): ?>
                                    <option value="<?= $tv['id'] ?>"><?= esc($tv['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Esquema de Servicio</label>
                            <select name="cat_esquema_servicio_id" class="form-select" required>
                                <option value="">-- Seleccionar Esquema --</option>
                                <?php foreach($esquemas as $e): ?>
                                    <option value="<?= $e['id'] ?>"><?= esc($e['nombre']) ?> (<?= esc($e['abreviatura']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Comisión Bruta ($)</label>
                            <input type="number" step="0.01" name="comision_bruta" class="form-control" value="0.00">
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="row">
                        <div class="col-md-6 mb-3 border-end">
                            <h6 class="fw-bold text-success mb-3"><i class="bi bi-file-earmark-check"></i> Cierre Comercial</h6>
                            <div class="mb-2">
                                <label class="form-label small text-muted">Monto Vendido ($)</label>
                                <input type="number" step="0.01" name="monto_vendido" class="form-control" required value="0.00">
                            </div>
                            <div>
                                <label class="form-label small text-muted">Fecha de Vendido</label>
                                <input type="date" name="fecha_vendido" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold text-info mb-3"><i class="bi bi-cash-coin"></i> Flujo de Caja (Banco)</h6>
                            <div class="mb-2">
                                <label class="form-label small text-muted">Monto Cobrado ($)</label>
                                <input type="number" step="0.01" name="monto_cobrado" class="form-control" value="0.00">
                            </div>
                            <div>
                                <label class="form-label small text-muted">Fecha de Cobrado</label>
                                <input type="date" name="fecha_cobrado" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>