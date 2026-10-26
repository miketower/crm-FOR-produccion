<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-0"><?= $titulo ?></h4>
        <small class="text-muted">Registro semanal de actividad y facturación por canal</small>
    </div>
    <div>
        <?php if ($canalAsignado): ?>
            <span class="badge bg-primary fs-6 px-3 py-2">
                <i class="bi bi-tag-fill me-1"></i> Canal: <?= $canalAsignado ?>
            </span>
        <?php else: ?>
            <span class="badge bg-secondary fs-6 px-3 py-2">
                <i class="bi bi-shield-lock-fill me-1"></i> Modo Administrador
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('msg') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- FORMULARIO DE CAPTURA -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-plus-circle text-primary me-2"></i>Nueva Carga Semanal
            </div>
            <div class="card-body">
                <form action="<?= base_url('carga-comercial/guardar') ?>" method="POST">
                    
                    <!-- CANAL Y FECHA -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Canal Comercial</label>
                        <?php if ($esAdmin): ?>
                            <select name="canal" class="form-select" required>
                                <option value="Optimización">Optimización</option>
                                <option value="Soluciones">Soluciones</option>
                                <option value="Otros">Otros</option>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control bg-light" value="<?= $canalAsignado ?>" readonly>
                            <input type="hidden" name="canal" value="<?= $canalAsignado ?>">
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Fecha de Cierre / Corte</label>
                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>


                    <hr class="text-muted">
                    <h6 class="fw-bold text-secondary small mb-3"><i class="bi bi-bar-chart-steps me-1"></i>Desglose de Citas por Origen</h6>

                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-bold">Asoc. FOR</label>
                            <input type="number" name="citas_asociados" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-bold">Venta Directa</label>
                            <input type="number" name="citas_directa" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-bold">Eventos</label>
                            <input type="number" name="citas_eventos" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-bold">Prosper</label>
                            <input type="number" name="citas_prosper" class="form-control" value="0" min="0" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Propuestas</label>
                            <input type="number" name="propuestas" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Cierres</label>
                            <input type="number" name="cierres" class="form-control" value="0" min="0" required>
                        </div>
                    </div>

                    <hr class="text-muted">
                    <h6 class="fw-bold text-secondary small mb-3"><i class="bi bi-cash-stack me-1"></i>Montos Financieros ($ MXN)</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Monto Vendido / Facturado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="monto_vendido" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Monto Cobrado (Ingreso Bruto)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="monto_cobrado" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ingreso Nuevo</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="ingreso_nuevo" class="form-control" placeholder="0.00" value="0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Observaciones / Notas</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Comentarios opcionales sobre la semana..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                        <i class="bi bi-cloud-upload me-2"></i>Guardar Carga Semanal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TABLA DE HISTORIAL -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-clock-history text-secondary me-2"></i>Historial de Cargas Registradas
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th class="ps-3">Fecha</th>
                                <th>Canal</th>
                                <th class="text-center">Actividad<br><small class="text-muted">(C / P / C)</small></th>
                                <th class="text-end">Monto Facturado</th>
                                <th class="text-end pe-3">Monto Cobrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historial)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay registros de cargas semanales aún.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historial as $h): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold small">
                                            <?= date('d/m/Y', strtotime($h['fecha'])) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= esc($h['canal']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center small">
                                            <span class="badge bg-info bg-opacity-10 text-info fw-bold me-1" title="Citas"><?= $h['citas'] ?></span>
                                            <span class="badge bg-warning bg-opacity-10 text-warning fw-bold me-1" title="Propuestas"><?= $h['propuestas'] ?></span>
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold" title="Cierres"><?= $h['cierres'] ?></span>
                                        </td>
                                        <td class="text-end small fw-bold text-primary">
                                            $<?= number_format($h['monto_vendido'], 2) ?>
                                        </td>
                                        <td class="text-end pe-3 small fw-bold text-success">
                                            $<?= number_format($h['monto_cobrado'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>