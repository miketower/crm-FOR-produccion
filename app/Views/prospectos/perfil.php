<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid px-0">
    
    <div class="card shadow-sm border-0 mb-4 border-start border-primary border-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex align-items-center mb-1">
                    <h2 class="fw-bold text-dark mb-0 me-3"><?= esc($prospect['company_name']) ?></h2>
                    
                    <a href="<?= base_url('prospectos/exportar/' . $prospect['id']) ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill fw-bold shadow-sm ms-2">
                        <i class="bi bi-file-pdf me-1"></i> Propuesta
                    </a>

                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill ms-2">
                        <?= esc($prospect['pipeline_stage']) ?>
                    </span>
                </div>
                <div class="text-muted small mb-2">
                    <i class="bi bi-briefcase me-1"></i> <?= esc($prospect['service_name'] ?? 'No asignado') ?>
                </div>
                
                <!-- INICIO: Mostrar Asistentes a la Cita -->
                <?php if(!empty($prospect['asistentes_cita'])): ?>
                    <div class="mt-2 p-2 bg-warning bg-opacity-10 rounded border border-warning-subtle d-inline-block">
                        <span class="fw-bold text-dark small me-2"><i class="bi bi-people-fill text-warning me-1"></i> Asistentes a la cita:</span>
                        <?php foreach(explode(',', $prospect['asistentes_cita']) as $asistente): ?>
                            <span class="badge bg-white text-dark border shadow-sm me-1">
                                <?= esc(trim($asistente)) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <!-- FIN: Mostrar Asistentes a la Cita -->
            </div>
            <div class="text-end">
                <div class="text-muted small mb-1">Valor Propuesto</div>
                <div class="fs-4 fw-bold text-success">$<?= number_format($prospect['ticket_value'], 2) ?></div>
            </div>
        </div>
    </div>

    <?php if(session()->getFlashdata('msg')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('msg') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Registrar Actividad</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('/prospectos/actividad/guardar') ?>" method="POST">
                            <input type="hidden" name="prospect_id" value="<?= $prospect['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Tipo de Acción</label>
                                <select name="activity_type" id="activity_type" class="form-select border-light bg-light" required onchange="toggleDate()">
                                    <option value="Nota">📝 Nota General</option>
                                    <option value="Llamada">📞 Llamada</option>
                                    <option value="Correo">✉️ Correo</option>
                                    <option value="Reunion">🤝 Reunión</option>
                                    <option value="Tarea">⏰ Tarea Pendiente</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Asunto / Resumen</label>
                                <input type="text" name="subject" class="form-control border-light bg-light" placeholder="Ej: Llamada de seguimiento..." required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Detalles / Notas</label>
                                <textarea name="notes" rows="3" class="form-control border-light bg-light" placeholder="Escribe aquí los detalles..."></textarea>
                            </div>

                            <div class="mb-4 d-none" id="div_due_date">
                                <label class="form-label fw-semibold text-danger small"><i class="bi bi-calendar-check me-1"></i>Fecha Límite</label>
                                <input type="datetime-local" name="due_date" class="form-control border-danger-subtle bg-danger bg-opacity-10">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                                Guardar Actividad
                            </button>
                        </form>
                    </div>
                </div>

                <!-- INICIO: Historial de Citas Agendadas -->
                <div class="card shadow-sm border-0 border-top border-warning border-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark"><i class="bi bi-calendar-check-fill me-2 text-warning"></i>Historial de Citas</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($citasHistorial)): ?>
                            <p class="text-center py-3 text-muted small italic mb-0">Sin citas registradas aún.</p>
                        <?php else: ?>
                            <div class="accordion" id="accordionCitas">
                                <?php foreach($citasHistorial as $index => $cita): ?>
                                    <div class="card border mb-2 shadow-none">
                                        <div class="card-header bg-light py-2" id="headingCita<?= $cita['id'] ?>">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-sm text-dark text-decoration-none fw-bold w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCita<?= $cita['id'] ?>" aria-expanded="true">
                                                    <span><i class="bi bi-building me-1"></i> <?= esc($cita['company_name']) ?></span>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><?= date('d/m/Y', strtotime($cita['created_at'])) ?></small>
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="collapseCita<?= $cita['id'] ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#accordionCitas">
                                            <div class="card-body small bg-white py-2">
                                                <p class="mb-1"><strong>Giro:</strong> <?= esc($cita['giro']) ?></p>
                                                <p class="mb-1"><strong>Tamaño:</strong> <?= esc($cita['company_size']) ?></p>
                                                <p class="mb-1"><strong>Atiende:</strong> <?= esc($cita['attendee_name']) ?> (<em><?= esc($cita['attendee_position']) ?></em>)</p>
                                                <?php if(!empty($cita['website'])): ?>
                                                    <p class="mb-1"><strong>Web:</strong> <a href="http://<?= esc($cita['website']) ?>" target="_blank"><?= esc($cita['website']) ?></a></p>
                                                <?php endif; ?>
                                                <p class="mb-1"><strong>Contexto:</strong> <?= esc($cita['context']) ?></p>
                                                <hr class="my-1">
                                                <p class="mb-0 text-muted" style="font-size: 0.75rem;">
                                                    <strong>Asistentes internos:</strong> <?= esc($cita['internal_attendees']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- FIN: Historial de Citas Agendadas -->

                <div class="card shadow-sm border-0 border-top border-info border-4">
    
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="fw-bold text-dark"><i class="bi bi-folder-fill me-2 text-info"></i>Expediente Digital</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('prospectos/documento/subir') ?>" method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="prospect_id" value="<?= $prospect['id'] ?>">
            
            <div class="mb-2">
                <select name="category" class="form-select form-select-sm border-info-subtle" required>
                    <option value="" selected disabled>Seleccionar tipo de documento...</option>
                    <option value="Identificación">Identificación Oficial / RFC</option>
                    <option value="Constitutivo">Acta Constitutiva</option>
                    <option value="Financiero">Estados Financieros / Opinión</option>
                    <option value="Contrato">Contratos / Propuestas Firmadas</option>
                    <option value="Otros">Otros anexos</option>
                </select>
            </div>

            <div class="input-group input-group-sm">
                <input type="file" name="prospect_file" class="form-control" required>
                <button class="btn btn-info text-white fw-bold" type="submit">Subir</button>
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">PDF, Excel, Word o Imágenes.</small>
        </form>

        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-hover align-middle">
                <tbody>
                    <?php if(empty($documentos)): ?>
                        <tr><td class="text-center py-4 text-muted small italic">Sin documentos adjuntos.</td></tr>
                    <?php else: ?>
                        <?php foreach($documentos as $doc): 
                            $ext = strtolower($doc['file_type']);
                            $icon = 'bi-file-earmark-text';
                            $color = 'text-secondary';

                            if ($ext === 'pdf') { $icon = 'bi-file-earmark-pdf-fill'; $color = 'text-danger'; }
                            elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = 'bi-file-earmark-excel-fill'; $color = 'text-success'; }
                            elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) { $icon = 'bi-file-earmark-image-fill'; $color = 'text-primary'; }
                        ?>
                        <tr>
    <td class="py-2">
        <div class="d-flex align-items-start">
            <i class="bi <?= $icon ?> <?= $color ?> fs-5 me-2 mt-1"></i>
            
            <div>
                <a href="<?= base_url('uploads/prospectos/' . $doc['file_name']) ?>" target="_blank" class="text-decoration-none text-dark small fw-semibold d-block">
                    <?= esc($doc['original_name']) ?>
                </a>

                <span class="badge bg-light text-secondary border rounded-pill mt-1" style="font-size: 0.6rem;">
                    <?= esc($doc['category'] ?? 'Otros') ?>
                </span>

                <div class="text-muted mt-1" style="font-size: 0.65rem;">
                    <i class="bi bi-person-circle me-1"></i>
                    Subido por: <?= esc($doc['user_name'] ?? 'Sistema') ?>
                </div>
            </div>
        </div>
    </td>

    <td class="text-end py-2">
        <div class="dropdown">
            <button class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li><a class="dropdown-item small" href="<?= base_url('uploads/prospectos/' . $doc['file_name']) ?>" download="<?= $doc['original_name'] ?>"><i class="bi bi-download me-2"></i>Descargar</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item small text-danger" href="<?= base_url('prospectos/documento/eliminar/' . $doc['id']) ?>" onclick="return confirm('¿Eliminar documento?')"><i class="bi bi-trash me-2"></i>Eliminar</a></li>
            </ul>
        </div>
        <div class="text-muted" style="font-size: 0.6rem;">
            <i class="bi bi-calendar3 me-1"></i><?= date('d/m/y', strtotime($doc['created_at'])) ?>
        </div>
    </td>
</tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-3">
                    <h5 class="fw-bold text-dark"><i class="bi bi-clock-history me-2 text-secondary"></i>Historial de Seguimiento</h5>
                </div>
                <div class="card-body pt-0">
                    
                    <?php if(empty($actividades)): ?>
                        <div class="text-center p-5 border border-dashed rounded bg-light mt-3">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">Aún no hay interacciones registradas.</p>
                        </div>
                    <?php else: ?>
                        <div class="position-relative mt-3 ms-3" style="border-left: 2px solid #f1f3f5;">
                            
                            <?php foreach($actividades as $act): ?>
                                <div class="position-relative mb-4" style="padding-left: 2.5rem;">
                                    
                                    <?php 
                                        $iconBg = 'bg-secondary';
                                        $icon = 'bi-sticky';
                                        if($act['activity_type'] == 'Llamada') { $iconBg = 'bg-success'; $icon = 'bi-telephone'; }
                                        if($act['activity_type'] == 'Correo') { $iconBg = 'bg-warning text-dark'; $icon = 'bi-envelope'; }
                                        if($act['activity_type'] == 'Reunion') { $iconBg = 'bg-info text-dark'; $icon = 'bi-people'; }
                                        if($act['activity_type'] == 'Tarea') { $iconBg = 'bg-danger'; $icon = 'bi-alarm'; }
                                    ?>
                                    <div class="position-absolute translate-middle-x <?= $iconBg ?> text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                         style="width: 32px; height: 32px; left: -1px; top: 0;">
                                        <i class="bi <?= $icon ?>" style="font-size: 0.9rem;"></i>
                                    </div>
                                    
                                    <div class="card border-0 bg-light shadow-none" style="border-radius: 12px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold text-dark mb-0"><?= esc($act['subject']) ?></h6>
                                                <span class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="bi bi-calendar3 me-1"></i><?= date('d M Y, h:i a', strtotime($act['created_at'])) ?>
                                                </span>
                                            </div>
                                            
                                            <p class="text-secondary small mb-3" style="white-space: pre-line; line-height: 1.5;">
                                                <?= esc($act['notes']) ?>
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top border-white">
                                                <small class="text-muted fw-semibold" style="font-size: 0.7rem;">
                                                    <i class="bi bi-person-circle me-1"></i> <?= esc($act['creator_name'] ?? 'Usuario CRM') ?>
                                                </small>
                                                
                                                <?php if($act['activity_type'] == 'Tarea' && $act['is_completed'] == 0): ?>
                                                    <a href="<?= base_url('/prospectos/actividad/completar/'.$act['id']) ?>" 
                                                       class="btn btn-sm btn-danger py-0 px-3 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                                        Completar ✅
                                                    </a>
                                                <?php elseif($act['activity_type'] == 'Tarea' && $act['is_completed'] == 1): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill" style="font-size: 0.65rem;">
                                                        <i class="bi bi-check-lg"></i> Hecho
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                            
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function toggleDate() {
        var tipo = document.getElementById('activity_type').value;
        var dateDiv = document.getElementById('div_due_date');
        if(tipo === 'Tarea') {
            dateDiv.classList.remove('d-none');
            dateDiv.querySelector('input').setAttribute('required', 'required');
        } else {
            dateDiv.classList.add('d-none');
            dateDiv.querySelector('input').removeAttribute('required');
        }
    }
</script>

<?= $this->endSection() ?>