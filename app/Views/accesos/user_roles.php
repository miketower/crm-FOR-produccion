<fieldset class="mb-3">
    <legend class="fs-6 fw-bold">Roles y accesos</legend>
    <p class="small text-muted">Puedes combinar varios roles. El usuario tendrá la suma de sus permisos.</p>
    <?php foreach ($roles as $role): ?>
        <label class="d-block mb-2">
            <input class="form-check-input me-2 user-role-checkbox" type="checkbox" name="roles[]" value="<?= (int) $role['id'] ?>"
                <?= in_array((int) $role['id'], $selectedRoles ?? [], true) ? 'checked' : '' ?>>
            <?= esc($role['name']) ?>
            <?= $role['slug'] === 'admin' ? '<span class="badge bg-dark ms-1">Acceso general y administración de usuarios</span>' : '' ?>
        </label>
    <?php endforeach; ?>
</fieldset>
<div class="mb-3">
    <label class="form-label" for="channel-<?= esc($formKey ?? 'nuevo', 'attr') ?>">Canal comercial</label>
    <select class="form-select" name="commercial_channel" id="channel-<?= esc($formKey ?? 'nuevo', 'attr') ?>">
        <option value="">Sin canal asignado</option>
        <?php foreach (['Soluciones', 'Optimización', 'Otros'] as $channel): ?>
            <option value="<?= esc($channel, 'attr') ?>" <?= ($selectedChannel ?? null) === $channel ? 'selected' : '' ?>><?= esc($channel) ?></option>
        <?php endforeach; ?>
    </select>
    <div class="form-text">Necesario para captura semanal si el rol no permite operar en todos los canales.</div>
</div>
