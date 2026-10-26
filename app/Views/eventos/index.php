<?= $this->extend('layout/main') ?> <!--[cite: 3] -->
<?= $this->section('contenido') ?> <!--[cite: 3] -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">Gestión de Eventos</h4>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEvento">
        <i class="bi bi-plus-circle"></i> Programar Evento
    </button>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div> <!--[cite: 3] -->
<?php endif; ?>

<!-- Pestañas para cambiar entre vistas -->
<ul class="nav nav-tabs mb-4" id="eventosTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-dark" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista" type="button" role="tab" aria-controls="lista" aria-selected="true">
            <i class="bi bi-list-task"></i> Lista
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="tarjetas-tab" data-bs-toggle="tab" data-bs-target="#tarjetas" type="button" role="tab" aria-controls="tarjetas" aria-selected="false">
            <i class="bi bi-grid"></i> Tarjetas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab" aria-controls="calendario" aria-selected="false">
            <i class="bi bi-calendar-month"></i> Calendario
        </button>
    </li>
</ul>

<div class="tab-content" id="eventosTabsContent">
    <!-- TAB 1: LISTA (TABLA) -->
    <div class="tab-pane fade show active" id="lista" role="tabpanel" aria-labelledby="lista-tab">
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Evento</th>
                            <th>Organizador</th>
                            <th>Fecha y Hora</th>
                            <th>Ubicación</th>
                            <th>Roles Solicitados</th>
                            <th>Estatus</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($eventos)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No hay eventos programados.</td></tr>
                        <?php endif; ?>
                        <?php foreach($eventos as $e): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?= esc($e['titulo']) ?></div>
                                <span class="badge <?= $e['categoria_evento'] == 'Externo' ? 'bg-primary' : 'bg-secondary' ?> small">
                                    <?= esc($e['categoria_evento']) ?>
                                </span>
                            </td>
                            <td><span class="text-secondary"><?= esc($e['organizador']) ?></span></td>
                            <td>
                                <i class="bi bi-calendar-event text-muted me-1"></i>
                                <?= date('d/m/Y h:i A', strtotime($e['fecha_evento'])) ?>
                            </td>
                            <td><i class="bi bi-geo-alt text-muted me-1"></i> <?= esc($e['ubicacion']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= esc($e['roles_asistencia'] ?: 'No definidos') ?></span></td>
                            <td><span class="badge <?= $e['estatus'] == 'Pendiente' ? 'bg-warning text-dark' : 'bg-success' ?>"><?= esc($e['estatus']) ?></span></td>
                            <td class="text-end pe-4">
                                <a href="<?= base_url('eventos/show/' . $e['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= base_url('eventos/editar/' . $e['id']) ?>" class="btn btn-sm btn-outline-primary ms-1" title="Editar Evento">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: CALENDARIO -->
    <div class="tab-pane fade" id="calendario" role="tabpanel" aria-labelledby="calendario-tab">
        <div class="card shadow border-0">
            <div class="card-body">
                <div id="miCalendario"></div>
            </div>
        </div>
    </div>

    <!-- TAB 3: TARJETAS (NUEVA) -->
    <div class="tab-pane fade" id="tarjetas" role="tabpanel" aria-labelledby="tarjetas-tab">
        <div class="row g-4">
            <?php if(empty($eventos)): ?>
                <div class="col-12 text-center py-5 text-muted">No hay eventos programados.</div>
            <?php endif; ?>
            
            <?php foreach($eventos as $e): 
                $timestamp = strtotime($e['fecha_evento']);
                $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                $mes = $meses[(int)date('m', $timestamp) - 1];
                $dia = date('d', $timestamp);
                
                // Colores para diferenciar como en la imagen
                $colorBandera = $e['categoria_evento'] == 'Externo' ? '#d9534f' : '#6f42c1'; 
            ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-0">
                    
                    <!-- Encabezado de la Tarjeta (Fecha y Decoración) -->
                    <div class="d-flex border-bottom" style="height: 70px;">
                        <div class="d-flex flex-column align-items-center justify-content-center text-white" 
                             style="width: 70px; background-color: <?= $colorBandera ?>; border-bottom-right-radius: 15px;">
                            <span class="fw-bold lh-1" style="font-size: 1.8rem;"><?= $dia ?></span>
                            <span class="small text-uppercase" style="font-size: 0.75rem;"><?= $mes ?></span>
                        </div>
                        <div class="flex-grow-1 p-2 d-flex flex-column justify-content-center text-end bg-light">
                            <span class="fw-bold text-dark small text-truncate d-block"><?= esc($e['organizador'] ?? $e['responsable'] ?? 'FOR CRM') ?></span>
                            <span class="text-muted" style="font-size: 0.7rem;">Comité | <?= esc($e['categoria_evento']) ?></span>
                        </div>
                    </div>

                    <!-- Cuerpo de la Tarjeta -->
                    <div class="card-body pt-3 pb-4">
                        <h5 class="fw-bold mb-3" style="min-height: 48px; line-height: 1.2; font-size: 1.1rem;">
                            <?= esc($e['titulo']) ?>
                        </h5>
                        
                        <div class="small text-muted mb-2">
                            <i class="bi bi-calendar-event text-primary me-1"></i> 
                            Fecha <?= $mes ?> <?= $dia ?>, <?= date('Y', $timestamp) ?>
                        </div>
                        
                        <div class="small text-muted mb-2">
                            <i class="bi bi-clock text-primary me-1"></i> 
                            Hora <?= date('g:i a', $timestamp) ?>
                        </div>
                        
                        <div class="small text-muted mb-2 text-truncate" title="<?= esc($e['ubicacion'] ?? 'Por definir') ?>">
                            <i class="bi bi-person-fill text-primary me-1"></i> 
                            Localidad <?= esc($e['ubicacion'] ?? 'Por definir') ?>
                        </div>
                        
                        <div class="small text-muted mb-4 text-truncate">
                            <i class="bi bi-bookmark-fill text-primary me-1"></i> 
                            Categoría <span class="text-info"><?= esc($e['objetivo'] ?? 'Evento institucional') ?></span>
                        </div>
                        
                        <a href="<?= base_url('eventos/show/' . $e['id']) ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" style="font-size: 0.8rem;">
                            <i class="bi bi-chevron-right me-1"></i> Más información
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Librería de FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('miCalendario');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es', // En español
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            list: 'Agenda'
        },
        events: [
            // Pasamos los eventos de PHP a Javascript para el calendario
            <?php foreach($eventos as $e): ?>
            {
                title: '<?= addslashes($e['titulo']) ?>',
                start: '<?= date('Y-m-d\TH:i:s', strtotime($e['fecha_evento'])) ?>',
                url: '<?= base_url('eventos/show/'.$e['id']) ?>',
                backgroundColor: '<?= $e['estatus'] == 'Pendiente' ? '#ffc107' : '#198754' ?>',
                borderColor: '<?= $e['estatus'] == 'Pendiente' ? '#ffc107' : '#198754' ?>',
                textColor: '<?= $e['estatus'] == 'Pendiente' ? '#000' : '#fff' ?>'
            },
            <?php endforeach; ?>
        ]
    });
    
    // Este pequeño script es necesario para que el calendario se ajuste correctamente 
    // al tamaño de la pantalla cuando cambiamos a la pestaña de "Calendario"
    var tabEl = document.querySelector('button[data-bs-target="#calendario"]');
    tabEl.addEventListener('shown.bs.tab', function (event) {
        calendar.render();
    });
});
</script>

<!-- MODAL NUEVO EVENTO UNIFICADO -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <form action="<?= base_url('eventos/guardar') ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Programar Nuevo Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <!-- SELECTOR PRINCIPAL -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="fw-bold text-primary mb-2">¿Qué tipo de evento vas a registrar?</label>
                            <select name="categoria_evento" id="categoria_evento" class="form-select form-select-lg" onchange="toggleCamposEvento()" required>
                                <option value="Externo">Evento Externo (Networking, Expos, etc.)</option>
                                <option value="Interno">Evento Interno (Capacitación, Integración, etc.)</option>
                            </select>
                        </div>
                    </div>

                    <!-- DATOS GENERALES (COMPARTIDOS) -->
                    <h6 class="fw-bold text-muted border-bottom pb-2">Datos Generales</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nombre del Evento</label>
                            <input type="text" name="titulo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Fecha y Hora</label>
                            <input type="datetime-local" name="fecha_evento" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ubicación</label>
                            <input type="text" name="ubicacion" class="form-control" placeholder="Física o Virtual">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Liga de Registro / Videollamada</label>
                            <input type="url" name="liga_registro" class="form-control" placeholder="https://...">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Objetivo del Evento</label>
                            <textarea name="objetivo" class="form-control" rows="2" placeholder="Networking, aprendizaje, etc."></textarea>
                        </div>
                    </div>

                    <!-- CAMPOS EXCLUSIVOS: EVENTO EXTERNO -->
                    <div id="seccion_externo">
                        <h6 class="fw-bold text-muted border-bottom pb-2 mt-2">Detalles del Evento Externo</h6>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Quién organiza</label>
                                <input type="text" name="organizador" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold">Costo (MXN)</label>
                                <input type="number" step="0.01" name="costo" class="form-control" placeholder="0.00">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-bold">Alcance</label>
                                <select name="alcance" class="form-select">
                                    <option value="Local">Local</option>
                                    <option value="Nacional">Nacional</option>
                                    <option value="Internacional">Internacional</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">Industria / Audiencia / Segmentación</label>
                                <textarea name="industria_segmentacion" class="form-control" rows="2" placeholder="Ej. Sector IT, Directores de RH..."></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="fw-bold d-block mb-2">¿Quién debe asistir de parte de FOR?</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="roles_asistencia[]" value="Especialistas" id="rolEspecialistas">
                                <label class="form-check-label" for="rolEspecialistas">Especialistas</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="roles_asistencia[]" value="Relaciones Públicas" id="rolRelaciones">
                                <label class="form-check-label" for="rolRelaciones">Relaciones Públicas</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="roles_asistencia[]" value="CEO" id="rolCEO">
                                <label class="form-check-label" for="rolCEO">CEO</label>
                            </div>
                        </div>
                    </div>

                    <!-- CAMPOS EXCLUSIVOS: EVENTO INTERNO -->
                    <div id="seccion_interno" class="d-none">
                        <h6 class="fw-bold text-muted border-bottom pb-2 mt-2">Detalles del Evento Interno</h6>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Responsable del Evento</label>
                                <input type="text" name="responsable" class="form-control" placeholder="Nombre de quien lo lidera">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Presupuesto Asignado (MXN)</label>
                                <input type="number" step="0.01" name="presupuesto" class="form-control" placeholder="0.00">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Tipo de Evento</label>
                                <input type="text" name="tipo_evento" class="form-control" placeholder="Ej. Capacitación">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Invitados</label>
                                <input type="text" name="invitados" class="form-control" placeholder="Ej. Todo el equipo">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Capacidad / Aforo</label>
                                <input type="number" name="capacidad" class="form-control" placeholder="Número de personas">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Evento</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Función para mostrar/ocultar campos dependiendo del tipo de evento seleccionado
    function toggleCamposEvento() {
        var tipo = document.getElementById('categoria_evento').value;
        var secExterno = document.getElementById('seccion_externo');
        var secInterno = document.getElementById('seccion_interno');

        if (tipo === 'Externo') {
            secExterno.classList.remove('d-none');
            secInterno.classList.add('d-none');
        } else {
            secExterno.classList.add('d-none');
            secInterno.classList.remove('d-none');
        }
    }
</script>

<?= $this->endSection() ?> <!--[cite: 3] -->