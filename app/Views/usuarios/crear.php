<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>
<div class="row justify-content-center"><div class="col-lg-9">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Registrar nuevo miembro</h5></div>
        <form class="card-body p-4" action="<?= base_url('usuarios/guardar') ?>" method="post">
            <?= csrf_field() ?>
            <?php if ($error = session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endif; ?>
            <div class="row"><div class="col-md-6">
                <div class="mb-3"><label for="new-user-name" class="form-label">Nombre completo</label><input id="new-user-name" name="full_name" class="form-control" maxlength="100" required autocomplete="name"></div>
                <div class="mb-3"><label for="new-user-email" class="form-label">Correo electrónico</label><input id="new-user-email" name="email" type="email" class="form-control" maxlength="100" required autocomplete="email"></div>
                <div class="mb-3"><label for="new-user-password" class="form-label">Contraseña</label><input id="new-user-password" name="password" type="password" class="form-control" minlength="8" maxlength="255" required autocomplete="new-password"><div class="form-text">Mínimo 8 caracteres.</div></div>
                <?= view('accesos/user_roles', ['roles' => $roles, 'selectedRoles' => []]) ?>
            </div><div class="col-md-6">
                <h6>El usuario tendrá estos accesos</h6>
                <div id="access-preview" class="bg-light rounded p-3" aria-live="polite">Selecciona al menos un rol.</div>
                <p class="small text-muted mt-2">Consultar permite abrir y descargar información. Modificar permite también registrar, editar y eliminar en ese módulo.</p>
                <a href="<?= base_url('usuarios/accesos') ?>">Configurar permisos de los roles</a>
            </div></div>
            <div class="d-flex gap-2 mt-3"><button class="btn btn-primary" type="submit">Guardar usuario</button><a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a></div>
        </form>
    </div>
</div></div>
<script>
(() => {
    const roles = <?= json_encode(array_column($roles, null, 'id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const modules = <?= json_encode($catalogo->modules, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const checkboxes = document.querySelectorAll('.user-role-checkbox');
    const preview = document.getElementById('access-preview');
    function render() {
        const selected = Array.from(checkboxes).filter(input => input.checked).map(input => roles[input.value]);
        const permissions = new Set(selected.flatMap(role => role.permissions));
        preview.replaceChildren();
        if (!selected.length) { preview.textContent = 'Selecciona al menos un rol.'; return; }
        if (selected.some(role => role.slug === 'admin')) {
            const notice = document.createElement('p'); notice.textContent = 'Administrador: acceso general, incluyendo usuarios y configuración de permisos. Algunas vistas privadas se autorizan por cuenta.'; preview.append(notice);
        }
        const list = document.createElement('ul'); list.className = 'mb-0';
        Object.entries(modules).forEach(([key, module]) => {
            if (!permissions.has(key + '.ver')) return;
            const item = document.createElement('li');
            item.textContent = module[0] + ': ' + (permissions.has(key + '.modificar') ? 'consultar y modificar' : 'solo consultar'); list.append(item);
        });
        if (!list.children.length) { preview.append(document.createTextNode('Este rol aún no tiene pantallas asignadas.')); }
        preview.append(list);
    }
    checkboxes.forEach(input => input.addEventListener('change', render)); render();
})();
</script>
<?= $this->endSection() ?>
