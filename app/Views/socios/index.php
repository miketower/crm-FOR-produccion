<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">Gestión de Asociados FOR y Convenios</h4>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSocio">
        <i class="bi bi-plus-circle"></i> Nuevo Socio
    </button>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>

<!-- PANEL DE FILTROS -->
<form action="<?= base_url('socios') ?>" method="GET" class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="buscar" class="form-control" placeholder="🔍 Buscar por nombre..." value="<?= esc($_GET['buscar'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <select name="semaforo" class="form-select" onchange="this.form.submit()">
                    <option value="">Filtrar por Semáforo...</option>
                    <?php foreach($semaforos as $sem): ?>
                        <option value="<?= $sem['id'] ?>" <?= ($_GET['semaforo'] ?? '') == $sem['id'] ? 'selected' : '' ?>>
                            <?= esc($sem['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </div>
</form>

<!-- TABLA DE SOCIOS -->
<div class="card shadow border-0">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Asociado FOR</th>
                    <th>Semáforo</th>
                    <th>Celular</th>
                    <th>Red (Invitado por)</th>
                    <th>Estatus Convenio</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($socios as $s): ?>
                <tr>
                    <td class="ps-4 fw-bold">
                        <a href="<?= base_url('socios/show/' . $s['id']) ?>" class="text-decoration-none text-dark"><?= esc($s['nombre']) ?></a>
                    </td>
                    <td>
                        <?php if($s['semaforo']): ?>
                            <span class="badge" style="background-color: <?= esc($s['color_hex']) ?>;"><?= esc($s['semaforo']) ?></span>
                        <?php else: ?>
                            <span class="text-muted small">Sin definir</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($s['celular']) ?></td>
                    <td class="text-muted"><?= esc($s['red_nombre'] ?? 'N/A') ?></td>
                    <td>
                        <span class="badge <?= ($s['estatus_convenio'] ?? 'Pendiente') == 'Realizado' ? 'bg-success' : 'bg-warning' ?>">
                            <?= esc($s['estatus_convenio'] ?? 'Pendiente') ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= base_url('socios/show/' . $s['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        <a href="<?= base_url('socios/editar/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Paginador Reconstruido -->
    <div class="card-footer bg-white py-3">
    <?= $pager->links('default', 'bootstrap_full') ?>
    <script>
        // Script de emergencia para inyectar los filtros a los links si el appends falla
        document.querySelectorAll('.pagination a').forEach(link => {
            const url = new URL(link.href);
            const params = new URLSearchParams(window.location.search);
            params.forEach((value, key) => {
                if(key !== 'page') url.searchParams.set(key, value);
            });
            link.href = url.toString();
        });
    </script>
    </div>
</div>
<div class="modal fade" id="modalSocio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <form action="<?= base_url('socios/guardar') ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Registrar Nuevo Asociado FOR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Celular</label>
                            <input type="text" name="celular" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Red (Invitado por)</label>
                            <select name="red_id" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach($todos_los_socios as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= esc($r['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Semáforo</label>
                            <select name="cat_semaforo_id" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach($semaforos as $sem): ?>
                                    <option value="<?= $sem['id'] ?>"><?= esc($sem['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Estatus Convenio</label>
                            <select name="estatus_convenio" class="form-select">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Realizado">Realizado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Responsable Resguardo</label>
                            <input type="text" name="responsable_resguardo" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Nivel de Participación</label>
                            <select name="nivel_participacion" class="form-select">
                                <option value="Activo">Activo</option>
                                <option value="Interesada">Interesada</option>
                                <option value="Comisiona">Comisiona</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Servicios que comisiona</label>
                            <select name="servicios_comisiona" class="form-select">
                                <option value="No aplica">No aplica</option>
                                <option value="Optimización">Optimización</option>
                                <option value="Soluciones">Soluciones</option>
                                <option value="Flujos">Flujos</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Tipo de Participación</label>
                            <select name="tipo_participacion" class="form-select">
                                <option value="Asociado FOR">Asociado FOR</option>
                                <option value="Unidad Estratégica">Unidad Estratégica</option>
                                <option value="Prosperhaus">Prosperhaus</option>
                                <option value="Socios Patrimoniales">Socios Patrimoniales</option>
                                <option value="Comunidad">Comunidad</option>
                                <option value="Socio Director">Socio Director</option>
                                <option value="FOR">FOR</option>
                            </select>
                        </div>
                    </div>
                    <!-- Agregamos la Fecha de Ingreso aquí -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Fecha de Ingreso</label>
                            <input type="date" name="fecha_entrada" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Notas</label>
                        <textarea name="notas" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Registro Completo</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>