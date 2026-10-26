<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Registrar Nuevo Miembro</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('usuarios/guardar') ?>" method="post">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="Ej. Juan Pérez">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico (Login)</label>
                        <input type="email" name="email" class="form-control" required placeholder="juan@empresa.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required placeholder="******">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol / Permisos</label>
                        <select name="role" class="form-select">
                            <option value="sales_rep">Vendedor (Solo ve sus datos)</option>
                            <option value="manager">Gerente</option>
                            <option value="admin">Administrador (Acceso Total)</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-light text-muted">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>