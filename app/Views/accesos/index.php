<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h4>Roles y accesos</h4><p class="text-muted mb-0">Configura las pantallas y acciones disponibles para cada perfil.</p></div>
    <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Volver al equipo</a>
</div>
<?php foreach (['msg' => 'success', 'error' => 'danger'] as $key => $style): if ($message = session()->getFlashdata($key)): ?>
    <div class="alert alert-<?= $style ?>" role="alert"><?= esc($message) ?></div>
<?php endif; endforeach; ?>
<div class="alert alert-info">Los cambios se aplican en la siguiente solicitud, sin pedir al usuario que cierre sesión. Solo los administradores pueden gestionar accesos. Los cambios de un rol afectan a todas las personas que lo tienen; crea otro rol si necesitas una combinación diferente.</div>
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#access-users" type="button" role="tab" aria-controls="access-users" aria-selected="true">Accesos por usuario</button></li>
    <li class="nav-item"><button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#access-roles" type="button" role="tab" aria-controls="access-roles" aria-selected="false">Configurar roles</button></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="access-users" role="tabpanel" aria-labelledby="users-tab">
        <?php if ($usuarios === []): ?><p>No hay usuarios registrados.</p><?php endif; ?>
        <?php foreach ($usuarios as $usuario): ?>
            <details class="card border-0 shadow-sm mb-3">
                <summary class="card-header bg-white p-3"><strong><?= esc($usuario['full_name']) ?></strong> <span class="text-muted ms-2"><?= esc($usuario['email']) ?></span><?= !$usuario['is_active'] ? ' (Inactivo)' : '' ?></summary>
                <form class="card-body" method="post" action="<?= base_url('usuarios/accesos/usuario/' . (int) $usuario['id']) ?>">
                    <?= csrf_field() ?>
                    <?= view('accesos/user_roles', ['roles' => $roles, 'selectedRoles' => $usuario['role_ids'], 'selectedChannel' => $usuario['commercial_channel'], 'formKey' => (string) $usuario['id']]) ?>
                    <button type="submit" class="btn btn-primary">Guardar accesos</button>
                </form>
            </details>
        <?php endforeach; ?>
    </div>
    <div class="tab-pane fade" id="access-roles" role="tabpanel" aria-labelledby="roles-tab">
        <?php foreach ($roles as $role): ?>
            <details class="card border-0 shadow-sm mb-3">
                <summary class="card-header bg-white p-3"><?= esc($role['name']) ?></summary>
                <?php if ($role['slug'] === 'admin'): ?>
                    <div class="card-body">El administrador tiene acceso total. Este perfil está protegido y no se pueden recortar sus permisos.</div>
                <?php else: ?>
                    <form class="card-body" method="post" action="<?= base_url('usuarios/accesos/roles/' . (int) $role['id']) ?>">
                        <?= csrf_field() ?>
                        <label class="form-label" for="role-name-<?= (int) $role['id'] ?>">Nombre del rol</label>
                        <input class="form-control mb-3" id="role-name-<?= (int) $role['id'] ?>" name="name" maxlength="80" required value="<?= esc($role['name'], 'attr') ?>">
                        <?= view('accesos/role_fields', ['catalogo' => $catalogo, 'selectedPermissions' => $role['permissions']]) ?>
                        <button type="submit" class="btn btn-primary">Guardar rol</button>
                    </form>
                <?php endif; ?>
            </details>
        <?php endforeach; ?>
        <details class="card border-primary mb-4">
            <summary class="card-header bg-white p-3 fw-bold">Crear un nuevo rol</summary>
            <form class="card-body" method="post" action="<?= base_url('usuarios/accesos/roles') ?>">
                <?= csrf_field() ?>
                <label class="form-label" for="new-role-name">Nombre del rol</label>
                <input class="form-control mb-3" id="new-role-name" name="name" maxlength="80" required placeholder="Ej. Consulta comercial">
                <?= view('accesos/role_fields', ['catalogo' => $catalogo, 'selectedPermissions' => []]) ?>
                <button type="submit" class="btn btn-primary">Crear rol</button>
            </form>
        </details>
    </div>
</div>
<?= $this->endSection() ?>
