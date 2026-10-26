<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-0">Historial de Notificaciones</h4>
        <small class="text-muted">Aquí se guardan todos tus avisos y alertas del sistema.</small>
    </div>
    
    <button class="btn btn-outline-secondary btn-sm" onclick="marcarTodoLeido()">
        <i class="bi bi-check-all"></i> Marcar todo como leído
    </button>
</div>

<div class="card shadow border-0">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            
            <?php if(empty($notificaciones)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash fs-1 text-muted mb-3"></i>
                    <p class="text-muted">No tienes notificaciones en el historial.</p>
                </div>
            <?php else: ?>
                
                <?php foreach($notificaciones as $n): ?>
                    <?php 
                        $bgClass = ($n['is_read'] == 0) ? 'bg-primary bg-opacity-10' : 'bg-white';
                        $iconColor = ($n['is_read'] == 0) ? 'text-primary' : 'text-muted';
                        
                        // Icono dinámico según el título
                        $icon = 'bi-info-circle';
                        if(strpos($n['title'], 'Meta') !== false) $icon = 'bi-trophy-fill text-warning';
                        if(strpos($n['title'], 'Prospecto') !== false) $icon = 'bi-person-plus-fill text-success';
                    ?>

                    <a href="<?= base_url($n['link']) ?>" class="list-group-item list-group-item-action p-3 d-flex align-items-center <?= $bgClass ?>">
                        
                        <div class="me-3 fs-4 <?= $iconColor ?>">
                            <i class="bi <?= $icon ?>"></i>
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold text-dark"><?= esc($n['title']) ?></h6>
                                <small class="text-muted"><?= date('d/m/Y h:i A', strtotime($n['created_at'])) ?></small>
                            </div>
                            <p class="mb-1 small text-secondary"><?= esc($n['message']) ?></p>
                        </div>

                        <div class="ms-3 text-muted">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    // Reutilizamos la API que ya creamos para marcar como leídas
    function marcarTodoLeido() {
        fetch('<?= base_url('api/notificaciones/leer') ?>', { method: 'POST' })
            .then(() => {
                location.reload(); // Recargar para quitar los fondos azules
            });
    }
</script>

<?= $this->endSection() ?>