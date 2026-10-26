<?= $this->extend('layout/main') ?> <!--[cite: 3] -->
<?= $this->section('contenido') ?> <!--[cite: 3] -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-dark mb-0">Eventos Internos (FOR Human Capital)</h4>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEvento">
        <i class="bi bi-plus-circle"></i> Organizar Evento
    </button>
</div>

<?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div> <!--[cite: 3] -->
<?php endif; ?>

<!-- Pestañas para cambiar entre Tabla y Calendario -->
<ul class="nav nav-tabs mb-4" id="eventosTabs" role="tablist"> <!--[cite: 3] -->
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-dark" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista" type="button" role="tab" aria-controls="lista" aria-selected="true"> <!--[cite: 3] -->
            <i class="bi bi-list-task"></i> Vista de Lista
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab" aria-controls="calendario" aria-selected="false"> <!--[cite: 3] -->
            <i class="bi bi-calendar-month"></i> Vista de Calendario
        </button>
    </li>
</ul>

<div class="tab-content" id="eventosTabsContent">
    <!-- TAB 1: LISTA (TABLA) -->
    <div class="tab-pane fade show active" id="lista" role="tabpanel" aria-labelledby="lista-tab"> <!--[cite: 3] -->
        <div class="card shadow border-0"> <!--[cite: 3] -->
            <div class="card-body p-0"> <!--[cite: 3] -->
                <table class="table table-hover align-middle mb-0"> <!--[cite: 3] -->
                    <thead class="bg-light"> <!--[cite: 3] -->
                        <tr>
                            <th class="ps-4">Evento</th>
                            <th>Responsable</th>
                            <th>Fecha y Hora</th>
                            <th>Ubicación</th>
                            <th>Invitados</th>
                            <th>Estatus</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($eventos)): ?> <!--[cite: 3] -->
                            <tr><td colspan="7" class="text-center py-4 text-muted">No hay eventos programados.</td></tr> <!--[cite: 3] -->
                        <?php endif; ?>
                        <?php foreach($eventos as $e): ?> <!--[cite: 3] -->
                        <tr>
                            <td class="ps-4 fw-bold text-dark"><?= esc($e['titulo']) ?></td> <!--[cite: 3] -->
                            <td><span class="text-secondary"><?= esc($e['responsable']) ?></span></td>
                            <td>
                                <i class="bi bi-calendar-event text-muted me-1"></i>
                                <?= date('d/m/Y h:i A', strtotime($e['fecha_evento'])) ?> <!--[cite: 3] -->
                            </td>
                            <td><i class="bi bi-geo-alt text-muted me-1"></i> <?= esc($e['ubicacion']) ?></td> <!--[cite: 3] -->
                            <td><span class="badge bg-info text-dark"><?= esc($e['invitados'] ?: 'No definidos') ?></span></td>
                            <td><span class="badge <?= $e['estatus'] == 'Planeación' ? 'bg-warning text-dark' : 'bg-success' ?>"><?= esc($e['estatus']) ?></span></td> <!--[cite: 3] -->
                            <td class="text-end pe-4">
                                <a href="<?= base_url('eventos_internos/show/' . $e['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Ver detalles">
                                    <i class="bi bi-eye"></i> <!--[cite: 3] -->
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?> <!--[cite: 3] -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: CALENDARIO -->
    <div class="tab-pane fade" id="calendario" role="tabpanel" aria-labelledby="calendario-tab"> <!--[cite: 3] -->
        <div class="card shadow border-0"> <!--[cite: 3] -->
            <div class="card-body"> <!--[cite: 3] -->
                <div id="miCalendario"></div> <!--[cite: 3] -->
            </div>
        </div>
    </div>
</div>

<!-- Librería de FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script> <!--[cite: 3] -->
<script>
document.addEventListener('DOMContentLoaded', function() { <!--[cite: 3] -->
    var calendarEl = document.getElementById('miCalendario'); <!--[cite: 3] -->
    
    var calendar = new FullCalendar.Calendar(calendarEl, { <!--[cite: 3] -->
        initialView: 'dayGridMonth', <!--[cite: 3] -->
        locale: 'es', <!--[cite: 3] -->
        headerToolbar: { <!--[cite: 3] -->
            left: 'prev,next today', <!--[cite: 3] -->
            center: 'title', <!--[cite: 3] -->
            right: 'dayGridMonth,timeGridWeek,listWeek' <!--[cite: 3] -->
        },
        buttonText: { <!--[cite: 3] -->
            today: 'Hoy', <!--[cite: 3] -->
            month: 'Mes', <!--[cite: 3] -->
            week: 'Semana', <!--[cite: 3] -->
            list: 'Agenda' <!--[cite: 3] -->
        },
        events: [
            <?php foreach($eventos as $e): ?> <!--[cite: 3] -->
            {
                title: '<?= addslashes($e['titulo']) ?>', <!--[cite: 3] -->
                start: '<?= date('Y-m-d\TH:i:s', strtotime($e['fecha_evento'])) ?>', <!--[cite: 3] -->
                url: '<?= base_url('eventos_internos/show/'.$e['id']) ?>', 
                backgroundColor: '<?= $e['estatus'] == 'Planeación' ? '#ffc107' : '#198754' ?>', 
                borderColor: '<?= $e['estatus'] == 'Planeación' ? '#ffc107' : '#198754' ?>',
                textColor: '<?= $e['estatus'] == 'Planeación' ? '#000' : '#fff' ?>'
            },
            <?php endforeach; ?> <!--[cite: 3] -->
        ]
    });
    
    var tabEl = document.querySelector('button[data-bs-target="#calendario"]'); <!--[cite: 3] -->
    tabEl.addEventListener('shown.bs.tab', function (event) { <!--[cite: 3] -->
        calendar.render(); <!--[cite: 3] -->
    });
});
</script>

<!-- MODAL NUEVO EVENTO -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true"> <!--[cite: 3] -->
    <div class="modal-dialog modal-lg"> <!--[cite: 3] -->
        <form action="<?= base_url('eventos_internos/guardar') ?>" method="POST" enctype="multipart/form-data">
            <div class="modal-content"> <!--[cite: 3] -->
                <div class="modal-header bg-light"> <!--[cite: 3] -->
                    <h5 class="modal-title fw-bold">Organizar Nuevo Evento Interno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!--[cite: 3] -->
                </div>
                <div class="modal-body"> <!--[cite: 3] -->
                    <!-- Fila 1: Datos Generales -->
                    <div class="row"> <!--[cite: 3] -->
                        <div class="col-md-6 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Nombre del Evento</label> <!--[cite: 3] -->
                            <input type="text" name="titulo" class="form-control" required> <!--[cite: 3] -->
                        </div>
                        <div class="col-md-6 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Responsable FOR</label>
                            <input type="text" name="responsable" class="form-control" required>
                        </div>
                    </div>

                    <!-- Fila 2: Logística y Costos -->
                    <div class="row"> <!--[cite: 3] -->
                        <div class="col-md-4 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Fecha y Hora</label> <!--[cite: 3] -->
                            <input type="datetime-local" name="fecha_evento" class="form-control" required> <!--[cite: 3] -->
                        </div>
                        <div class="col-md-4 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Ubicación</label> <!--[cite: 3] -->
                            <input type="text" name="ubicacion" class="form-control" placeholder="Física o Virtual"> <!--[cite: 3] -->
                        </div>
                        <div class="col-md-4 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Presupuesto (MXN)</label>
                            <input type="number" step="0.01" name="presupuesto" class="form-control" placeholder="0.00"> <!--[cite: 3] -->
                        </div>
                    </div>

                    <!-- Fila 3: Estrategia -->
                    <div class="row"> <!--[cite: 3] -->
                        <div class="col-md-6 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Objetivo del Evento</label> <!--[cite: 3] -->
                            <textarea name="objetivo" class="form-control" rows="2" placeholder="Ej. Integración de equipo"></textarea> <!--[cite: 3] -->
                        </div>
                        <div class="col-md-6 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Tipo de Evento</label>
                            <select name="tipo_evento" class="form-select"> <!--[cite: 3] -->
                                <option value="Capacitación">Capacitación</option>
                                <option value="Integración (Team Building)">Integración (Team Building)</option>
                                <option value="Evento Social / Fiesta">Evento Social / Fiesta</option>
                                <option value="Reunión Estratégica">Reunión Estratégica</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 4: Registro y Capacidad -->
                    <div class="row"> <!--[cite: 3] -->
                        <div class="col-md-8 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Liga de Confirmación / Registro</label>
                            <input type="url" name="liga_registro" class="form-control" placeholder="https://..."> <!--[cite: 3] -->
                        </div>
                        <div class="col-md-4 mb-3"> <!--[cite: 3] -->
                            <label class="fw-bold">Capacidad / Aforo</label>
                            <input type="number" name="capacidad" class="form-control" placeholder="No. de personas">
                        </div>
                    </div>

                    <!-- Fila Adicional: Segmentación y Publicidad -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Tipo de Asistente</label>
                            <input type="text" name="tipo_asistente" class="form-control" placeholder="Ej. Analistas, Directivos...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Industria a la que va dirigido</label>
                            <input type="text" name="industria_dirigido" class="form-control" placeholder="Ej. Tecnología, RH, Manufactura...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Flyer (PDF o Imagen)</label>
                            <!-- El campo tipo "file" permite subir el documento -->
                            <input type="file" name="flyer" class="form-control" accept="image/*,.pdf">
                        </div>
                    </div>

                    <!-- Fila 5: Audiencia -->
                    <div class="mb-3"> <!--[cite: 3] -->
                        <label class="fw-bold d-block mb-2">¿A quiénes vamos a invitar?</label>
                        <div class="form-check form-check-inline"> <!--[cite: 3] -->
                            <input class="form-check-input" type="checkbox" name="invitados[]" value="Todo el Equipo" id="invEquipo"> <!--[cite: 3] -->
                            <label class="form-check-label" for="invEquipo">Todo el Equipo</label> <!--[cite: 3] -->
                        </div>
                        <div class="form-check form-check-inline"> <!--[cite: 3] -->
                            <input class="form-check-input" type="checkbox" name="invitados[]" value="Directivos" id="invDirectivos"> <!--[cite: 3] -->
                            <label class="form-check-label" for="invDirectivos">Directivos</label> <!--[cite: 3] -->
                        </div>
                        <div class="form-check form-check-inline"> <!--[cite: 3] -->
                            <input class="form-check-input" type="checkbox" name="invitados[]" value="Clientes VIP" id="invClientes"> <!--[cite: 3] -->
                            <label class="form-check-label" for="invClientes">Clientes VIP</label> <!--[cite: 3] -->
                        </div>
                        <div class="form-check form-check-inline"> <!--[cite: 3] -->
                            <input class="form-check-input" type="checkbox" name="invitados[]" value="Proveedores" id="invProveedores"> <!--[cite: 3] -->
                            <label class="form-check-label" for="invProveedores">Proveedores</label> <!--[cite: 3] -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer"> <!--[cite: 3] -->
                    <button type="submit" class="btn btn-primary">Guardar Evento</button> <!--[cite: 3] -->
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?> <!--[cite: 3] -->