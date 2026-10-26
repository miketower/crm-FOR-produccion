<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">Gestión de Equipo</h4>
    <a href="<?= base_url('usuarios/accesos') ?>" class="btn btn-outline-primary ms-auto me-2">Roles y accesos</a>
    <a href="<?= base_url('usuarios/nuevo') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-person-plus-fill"></i> Nuevo Usuario
    </a>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('msg')) ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card shadow border-0">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Nombre</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha Alta</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3" style="width: 40px; height: 40px;">
                                <?= substr($u['full_name'], 0, 1) ?>
                            </div>
                            <div>
                                <div class="fw-bold"><?= esc($u['full_name']) ?></div>
                                <div class="small text-muted"><?= esc($u['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php foreach ($u['role_names'] as $roleName): ?>
                            <span class="badge bg-info text-dark"><?= esc($roleName) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php if($u['is_active']): ?>
                            <span class="badge bg-success bg-opacity-10 text-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                    </td>
                    <td class="text-end pe-4">
                        <a href="<?= base_url('usuarios/accesos') ?>" class="btn btn-sm btn-outline-primary">Configurar accesos</a>
                        <form class="d-inline" method="post" action="<?= base_url('usuarios/eliminar/' . (int) $u['id']) ?>" onsubmit="return confirm('¿Seguro que deseas eliminar a este usuario?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Eliminar usuario"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>