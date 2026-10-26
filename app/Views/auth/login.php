<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - CRM FOR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { width: 100%; max-width: 400px; border: none; border-radius: 12px; }
        .btn-primary { background-color: #0d6efd; padding: 10px; font-weight: bold; }
    </style>
</head>
<body>

<div class="card shadow-lg p-4">
    <div class="card-body">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">CRM FOR</h3>
            <p class="text-muted">Ingresa tus credenciales</p>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger text-center">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/login') ?>" method="post">
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" placeholder="user@forhumancapital.mx" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="******" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Entrar al Sistema</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>