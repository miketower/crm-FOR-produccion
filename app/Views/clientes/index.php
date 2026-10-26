<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-0"><?= $titulo ?></h4>
        <small class="text-muted">Empresas en fase de contratación y operación activa.</small>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('clientes') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Buscar Cliente</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Razón Social o RFC..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Fecha Inicio (Cierre)</label>
                <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="<?= $fecha_inicio ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Fecha Fin (Cierre)</label>
                <input type="date" name="fecha_fin" class="form-control form-control-sm" value="<?= $fecha_fin ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary btn-sm fw-bold">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light text-uppercase font-monospace text-muted" style="font-size: 11px;">
                    <tr>
                        <th class="ps-3">Razón Social</th>
                        <th>RFC</th>
                        <th>Servicio</th>
                        <th>Contacto</th>
                        <th>Ticket Mensual</th>
                        <th>Fecha Cierre</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($clientes)): ?>
                        <?php foreach($clientes as $c): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark"><?= esc($c['company_name']) ?></td>
                                <td class="font-monospace fw-bold text-secondary"><?= esc($c['rfc'] ?? '❌ SIN RFC') ?></td>
                                <td><span class="badge bg-opacity-10 bg-primary text-primary fw-bold px-2 py-1"><?= esc($c['service_name'] ?? 'No asignado') ?></span></td>
                                <td>
                                    <div class="fw-bold"><?= esc($c['nombre_contacto'] ?? 'N/A') ?></div>
                                    <small class="text-muted"><?= esc($c['correo_electronico'] ?? '') ?></small>
                                </td>
                                <td class="fw-bold text-success">$<?= number_format($c['ticket_value'], 2) ?></td>
                                <td class="text-muted"><?= $c['last_stage_change_date'] ? date('d/m/Y', strtotime($c['last_stage_change_date'])) : 'N/A' ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <!-- Botón Ver Detalle -->
                                        <a href="<?= base_url('clientes/detalle/'.$c['id']) ?>" class="btn btn-sm btn-outline-info" title="Ver Expediente">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <!-- Botón Completar Datos -->
                                        <a href="<?= base_url('clientes/editar/'.$c['id']) ?>" class="btn btn-sm btn-primary" title="Completar Alta de Excel">
                                            <i class="bi bi-pencil-square"></i> Completar Info
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                No se encontraron clientes cerrados ganados en este rango de fechas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <?= $pager ?>
</div>

<?= $this->endSection() ?>