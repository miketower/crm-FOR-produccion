<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">Mi Perfil</h4>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle"></i> <?= session()->getFlashdata('msg') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm"><i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<form action="<?= base_url('perfil/actualizar') ?>" method="post" id="formPerfil" novalidate>
    <div class="row">
        
        <div class="col-md-6">
            <div class="card shadow border-0 mb-4 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-badge"></i> Información Personal</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" id="full_name" class="form-control" value="<?= esc($usuario['full_name']) ?>" required>
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc($usuario['email']) ?>" required>
                        <div class="invalid-feedback" id="emailError">Ingresa un correo válido.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rol Asignado</label>
                        <input type="text" class="form-control bg-light" value="<?= ucfirst($usuario['role']) ?>" readonly disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow border-0 mb-4 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-shield-lock"></i> Cambiar Contraseña</h6>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border text-muted small">
                        <i class="bi bi-info-circle"></i> Deja estos campos vacíos si <strong>NO</strong> deseas cambiar tu contraseña.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••" minlength="6">
                        <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••">
                        <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex justify-content-end mt-2">
        <a href="<?= base_url('prospectos') ?>" class="btn btn-light me-2">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4 shadow">
            <i class="bi bi-save"></i> Guardar Cambios
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formPerfil');
        
        form.addEventListener('submit', function (event) {
            let isValid = true;
            
            // 1. Limpiar validaciones previas
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(input => input.classList.remove('is-invalid'));

            // 2. Validar Nombre
            const nameInput = document.getElementById('full_name');
            if (!nameInput.value.trim()) {
                nameInput.classList.add('is-invalid');
                isValid = false;
            }

            // 3. Validar Email (Formato Regex)
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                isValid = false;
            }

            // 4. Validar Contraseñas
            const passInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');

            // Solo validamos si el usuario escribió algo en "Nueva Contraseña"
            if (passInput.value.length > 0) {
                // Longitud mínima
                if (passInput.value.length < 6) {
                    passInput.classList.add('is-invalid');
                    isValid = false;
                }
                // Coincidencia
                if (passInput.value !== confirmInput.value) {
                    confirmInput.classList.add('is-invalid');
                    isValid = false;
                }
            }

            // Si hay errores, no enviamos el formulario
            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
</script>

<?= $this->endSection() ?>