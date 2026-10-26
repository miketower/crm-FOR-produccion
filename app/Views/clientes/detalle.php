<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="card shadow border-0">
    <div class="card-body">
        <!-- Encabezado con botón de edición -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><?= esc($cliente['razon_social']) ?></h4>
            <a href="<?= base_url('clientes/editar/' . $cliente['id']) ?>" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Editar Cliente
            </a>
        </div>
        
        <p class="text-muted">Nombre Comercial: <?= esc($cliente['nombre_comercial']) ?></p>
        
        <h6 class="mt-4 fw-bold">Esquemas Asignados:</h6>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach($esquemas as $e): ?>
                <span class="badge bg-primary"><?= esc($e->abreviatura ?? $e->nombre) ?></span>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-4">
            <a href="<?= base_url('clientes') ?>" class="btn btn-secondary">Regresar</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>