<?php $selectedPermissions = $selectedPermissions ?? []; ?>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead><tr><th>Pantalla</th><th class="text-center">Consultar</th><th class="text-center">Modificar</th></tr></thead>
    <tbody>
    <?php foreach ($catalogo->modules as $key => $module): ?>
        <tr>
            <td><?= esc($module[0]) ?></td>
            <?php foreach (['ver', 'modificar'] as $action): $permission = $key . '.' . $action; ?>
                <td class="text-center"><input class="form-check-input" type="checkbox" name="permissions[]" value="<?= esc($permission, 'attr') ?>"
                    aria-label="<?= esc($module[0] . ': ' . ($action === 'ver' ? 'consultar' : 'modificar'), 'attr') ?>"
                    <?= in_array($permission, $selectedPermissions, true) ? 'checked' : '' ?>></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<p class="small text-muted">Modificar incluye altas, ediciones y eliminaciones del módulo, y activa también Consultar. Consultar permite ver y descargar información.</p>
<details class="mb-3"><summary>Alcance de datos y funciones especiales</summary>
    <p class="small text-muted mt-2">Estos permisos amplían el alcance de los listados existentes. Asígnalos solo cuando corresponda.</p>
    <?php foreach ($catalogo->scopes as $permission => $label): ?>
        <label class="d-block mb-2"><input class="form-check-input me-2" type="checkbox" name="permissions[]" value="<?= esc($permission, 'attr') ?>" <?= in_array($permission, $selectedPermissions, true) ? 'checked' : '' ?>><?= esc($label) ?></label>
    <?php endforeach; ?>
</details>
