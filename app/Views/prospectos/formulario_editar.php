<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-0">Editar: <?= esc($prospect['company_name']) ?></h4>
        <small class="text-muted">Actualiza la información y avanza en el embudo.</small>
    </div>
    <a href="<?= base_url('prospectos') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Regresar
    </a>
</div>

<div class="card shadow-sm mb-4 sticky-top" style="top: 10px; z-index: 1020; border-left: 5px solid #ccc;" id="viabilityCard">
    <div class="card-body py-2 d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted small text-uppercase fw-bold">Clasificación Automática:</span>
            <h2 class="mb-0 fw-bold" id="classificationText">--</h2>
        </div>
        <div class="w-50 text-end">
            <span id="scoreText" class="small fw-bold text-muted">0 pts</span>
            <div class="progress" style="height: 10px;">
                <div id="viabilityBar" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow border-0">
    <div class="card-body p-4">

        
        <form action="<?= base_url('prospectos/actualizar/'.$prospect['id']) ?>" method="post">
            
        <div class="alert alert-primary d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-funnel-fill fs-3 me-3"></i>
            
            <!-- Columna del Pipeline -->
            <div class="flex-grow-1 me-3">
                <label class="fw-bold mb-1">Etapa del Embudo (Pipeline)</label>
                <select id="pipeline_stage" name="pipeline_stage" class="form-select form-select-sm fw-bold text-primary">
                    <option value="Nuevo" <?= $prospect['pipeline_stage'] == 'Nuevo' ? 'selected' : '' ?>>1. Nuevo Lead</option>
                    <option value="Contactado Pendiente" <?= $prospect['pipeline_stage'] == 'Contactado Pendiente' ? 'selected' : '' ?>>2. Contactado</option>
                    <option value="Contactado" <?= $prospect['pipeline_stage'] == 'Contactado' ? 'selected' : '' ?>>3. Cita</option>
                    <option value="Solicitud de informacion" <?= $prospect['pipeline_stage'] == 'Solicitud de informacion' ? 'selected' : '' ?>>4. Solicitud de Información</option>
                    <option value="Propuesta" <?= $prospect['pipeline_stage'] == 'Propuesta' ? 'selected' : '' ?>>5. Propuesta Enviada</option>
                    <option value="Negociacion" <?= $prospect['pipeline_stage'] == 'Negociacion' ? 'selected' : '' ?>>6. En Negociación</option>
                    <option value="Cerrado Ganado" <?= $prospect['pipeline_stage'] == 'Cerrado Ganado' ? 'selected' : '' ?>>🎉 7. Cerrado GANADO</option>
                    <option value="Cerrado Perdido" <?= $prospect['pipeline_stage'] == 'Cerrado Perdido' ? 'selected' : '' ?>>❌ 8. Cerrado Perdido</option>
                    <option value="Cerrado sin decisión" <?= $prospect['pipeline_stage'] == 'Cerrado sin decisión' ? 'selected' : '' ?>>💤 9. Cerrado sin decisión</option>
                </select>
            </div>

            <!-- Columna de la Fecha (Mismo estilo compacto) -->
            <div style="width: 150px;">
                <label class="fw-bold mb-1">Fecha del Evento</label>
                <input type="date" 
                    name="stage_change_date" 
                    id="stage_change_date" 
                    class="form-control form-control-sm fw-bold" 
                    value="<?= $prospect['last_stage_change_date'] ?? date('Y-m-d') ?>" 
                    required>
            </div>
        </div>

        <!-- INICIO: Selección de Asistentes para Cita (Oculto por defecto) -->
        <div id="div_asistentes_cita" class="alert alert-warning mb-4 d-none">
            <label class="fw-bold mb-2 text-dark"><i class="bi bi-people-fill me-2"></i>¿Quién debe asistir a esta cita?</label>
            <div class="bg-white p-3 rounded border">
                <?php 
                    $asistentes_guardados = explode(',', $prospect['asistentes_cita'] ?? '');
                    $asistentes_guardados = array_map('trim', $asistentes_guardados);
                ?>
                <div class="form-check form-check-inline me-4">
                    <input class="form-check-input" type="checkbox" name="asistentes_cita[]" value="Jorge Blanco" id="asc_jorge" <?= in_array('Jorge Blanco', $asistentes_guardados) ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark" for="asc_jorge">Jorge Blanco</label>
                </div>
                <div class="form-check form-check-inline me-4">
                    <input class="form-check-input" type="checkbox" name="asistentes_cita[]" value="Alicia Gutierrez" id="asc_alicia" <?= in_array('Alicia Gutierrez', $asistentes_guardados) ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark" for="asc_alicia">Alicia Gutierrez</label>
                </div>
                <div class="form-check form-check-inline me-4">
                    <input class="form-check-input" type="checkbox" name="asistentes_cita[]" value="Victoria Torres" id="asc_victoria" <?= in_array('Victoria Torres', $asistentes_guardados) ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark" for="asc_victoria">Victoria Torres</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="asistentes_cita[]" value="Fernando González" id="asc_fernando" <?= in_array('Fernando González', $asistentes_guardados) ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark" for="asc_fernando">Fernando González</label>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Puedes seleccionar a más de una persona.</small>
        </div>
        <!-- FIN: Selección de Asistentes -->

        <!-- INICIO: Formulario de Cita Obligatorio (Oculto por defecto) -->
        <div id="div_datos_cita" class="card border-warning mb-4 d-none shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 text-dark fw-bold">
                <i class="bi bi-calendar-event-fill me-2"></i>Detalles Obligatorios de la Cita / Contacto
            </div>
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nombre de la empresa <span class="text-danger">*</span></label>
                        <input type="text" name="cita_company_name" id="cita_company_name" class="form-control form-control-sm" value="<?= esc($prospect['company_name']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Giro de la empresa <span class="text-danger">*</span></label>
                        <input type="text" name="cita_giro" id="cita_giro" class="form-control form-control-sm" placeholder="Ej. Tecnología, Manufactura...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tamaño de la empresa <span class="text-danger">*</span></label>
                        <input type="text" name="cita_company_size" id="cita_company_size" class="form-control form-control-sm" placeholder="Ej. 50-100 empleados">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Quién va a atender la cita <span class="text-danger">*</span></label>
                        <input type="text" name="cita_attendee_name" id="cita_attendee_name" class="form-control form-control-sm" placeholder="Nombre del contacto del cliente">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Puesto que tiene <span class="text-danger">*</span></label>
                        <input type="text" name="cita_attendee_position" id="cita_attendee_position" class="form-control form-control-sm" placeholder="Ej. Director de RH">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Página web</label>
                        <input type="text" name="cita_website" class="form-control form-control-sm" placeholder="www.empresa.com">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Contexto sobre la cita <span class="text-danger">*</span></label>
                        <textarea name="cita_context" id="cita_context" class="form-control form-control-sm" rows="2" placeholder="Objetivo, temas a tratar, antecedentes..."></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-bold d-block">Quiénes del equipo deben asistir <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input cita-chk" type="checkbox" name="asistentes_cita[]" value="Jorge Blanco" id="asc_jorge">
                            <label class="form-check-label text-dark small" for="asc_jorge">Jorge Blanco</label>
                        </div>
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input cita-chk" type="checkbox" name="asistentes_cita[]" value="Alicia Gutierrez" id="asc_alicia">
                            <label class="form-check-label text-dark small" for="asc_alicia">Alicia Gutierrez</label>
                        </div>
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input cita-chk" type="checkbox" name="asistentes_cita[]" value="Victoria Torres" id="asc_victoria">
                            <label class="form-check-label text-dark small" for="asc_victoria">Victoria Torres</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input cita-chk" type="checkbox" name="asistentes_cita[]" value="Fernando González" id="asc_fernando">
                            <label class="form-check-label text-dark small" for="asc_fernando">Fernando González</label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- FIN: Formulario de Cita -->

            <h5 class="section-title text-muted border-bottom pb-2">1. Datos de la Empresa</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nombre de la Empresa</label>
                    <input type="text" name="company_name" class="form-control" value="<?= esc($prospect['company_name']) ?>" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Sector / Categoría</label>
                    <select name="industry_id" id="macro_category" class="form-select calc-trigger" required>
                        <option value="">Seleccione...</option>
                        
                        <?php if(!empty($categorias)): ?>
                            <?php foreach($categorias as $c): ?>
                                <?php 
                                    // Verificamos si esta categoría es la que tiene el prospecto
                                    $selected = ($c['id'] == $currentMacroId) ? 'selected' : ''; 
                                ?>
                                <option value="<?= $c['id'] ?>" <?= $selected ?>>
                                    <?= esc($c['nombre_categoria']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-primary">Tipo de Servicio</label>
                    <select name="service_type_id" id="service_type" class="form-select calc-trigger" required>
                        <option value="">Seleccione...</option>
                        <?php if(!empty($servicios)): ?>
                            <?php foreach($servicios as $s): ?>
                                <?php 
                                    // COMPARACIÓN: ¿El ID de la lista es igual al que tiene el prospecto?
                                    $selected = ($s['id'] == $prospect['service_type_id']) ? 'selected' : ''; 
                                ?>
                                <option value="<?= $s['id'] ?>" <?= $selected ?>>
                                    <?= esc($s['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-primary">Canal / Tipo de Venta</label>
                    <select name="cat_tipo_venta_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php if(!empty($tipos_ventas)): ?>
                            <?php foreach($tipos_ventas as $tv): ?>
                                <option value="<?= $tv['id'] ?>" <?= ($prospect['cat_tipo_venta_id'] == $tv['id']) ? 'selected' : '' ?>>
                                    <?= esc($tv['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Asociado FOR</label>
                    <select name="comisionista_id" class="form-select">
                        <option value="">Sin Asociado...</option>
                        <?php if(!empty($socios)): ?>
                            <?php foreach($socios as $socio): ?>
                                <option value="<?= $socio['id'] ?>" <?= ($prospect['comisionista_id'] == $socio['id']) ? 'selected' : '' ?>>
                                    <?= esc($socio['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado (Ubicación)</label>
                    <select name="state_id" id="state_selector" class="form-select calc-trigger" required>
                        <option value="">Seleccione...</option>
                        <?php if(!empty($estados)): ?>
                            <?php foreach($estados as $edo): ?>
                                <?php 
                                    // COMPARACIÓN: ¿El ID del estado es igual al guardado?
                                    $selected = ($edo['id'] == $prospect['state_id']) ? 'selected' : ''; 
                                ?>
                                <option value="<?= $edo['id'] ?>" <?= $selected ?>>
                                    <?= esc($edo['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Empleados</label>
                    <input type="number" name="employees_count" class="form-control" value="<?= esc($prospect['employees_count']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ticket ($)</label>
                    <input type="number" step="0.01" name="ticket_value" id="ticket" class="form-control calc-trigger" value="<?= esc($prospect['ticket_value']) ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Complejidad (1-5)</label>
                    <input type="number" name="complexity_level" class="form-control calc-trigger" value="<?= esc($prospect['complexity_level']) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Duración (Meses)</label>
                    <input type="number" name="duration_months" class="form-control" value="<?= esc($prospect['duration_months']) ?>">
                </div>
            </div>

            <h5 class="section-title text-muted border-bottom pb-2">2. Finanzas</h5>
            <div class="bg-light p-3 rounded mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Ingreso Total ($)</label>
                        <input type="number" step="0.01" name="total_income_charged" class="form-control" value="<?= esc($prospect['total_income_charged']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">% Comisión</label>
                        <input type="number" step="0.01" name="commission_percent" class="form-control" value="<?= esc($prospect['commission_percent']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Costos Directos ($)</label>
                        <input type="number" step="0.01" name="direct_costs" class="form-control" value="<?= esc($prospect['direct_costs']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Costos Indirectos ($)</label>
                        <input type="number" step="0.01" name="indirect_costs" class="form-control" value="<?= esc($prospect['indirect_costs']) ?>">
                    </div>
                </div>
            </div>

            <h5 class="section-title text-muted border-bottom pb-2">3. Decisión</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Primer Contacto</label>
                    <input type="text" name="first_contact_role" class="form-control" value="<?= esc($prospect['first_contact_role']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Decisor</label>
                    <select name="decision_maker_role" id="decisor" class="form-select calc-trigger">
                        <option value="">Seleccione...</option>
                        <?php $roles = ['Dueño/Socio', 'CFO', 'Líder Admin/Finanzas', 'Dir. General', 'Contador', 'Líder RH', 'Otro']; ?>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r ?>" <?= $prospect['decision_maker_role'] == $r ? 'selected' : '' ?>><?= $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Perfil Psicológico</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check calc-trigger" name="psychological_profile" id="p1" value="Conservador" <?= $prospect['psychological_profile'] == 'Conservador' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-secondary" for="p1">Conservador</label>

                        <input type="radio" class="btn-check calc-trigger" name="psychological_profile" id="p2" value="Estrategico" <?= $prospect['psychological_profile'] == 'Estrategico' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-secondary" for="p2">Estratégico</label>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label">Observaciones Generales</label>
                <textarea name="general_observations" class="form-control" rows="3"><?= esc($prospect['general_observations']) ?></textarea>
            </div>

            <div class="d-grid gap-2 mb-5">
                <button type="submit" class="btn btn-success btn-lg shadow">
                    <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
            </div>
            <input type="hidden" name="classification_group" id="classification_hidden" value="D">
        </form>
    </div>
</div>

<script>
    // Variables de PHP (Historial)
    const historicalMacro   = <?= json_encode($pesosMacro ?? []) ?>;
    const historicalState   = <?= json_encode($pesosEstado ?? []) ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // ----------------------------------------------------
        // 1. CÁLCULO DE VIABILIDAD
        // ----------------------------------------------------
        const allInputs = document.querySelectorAll('.calc-trigger, select, input'); 
        allInputs.forEach(input => {
            input.addEventListener('change', calculateViability);
            input.addEventListener('input', calculateViability);
        });

        calculateViability(); // Ejecutar al inicio

        function calculateViability() {
            let score = 0;

            // TICKET PROMEDIO (70 pts)
            const ticketInput = parseFloat(document.getElementById('ticket').value) || 0;
            const metaTicket = 1500000; 
            if (ticketInput >= metaTicket) {
                score += 70;
            } else {
                score += (ticketInput / metaTicket) * 70;
            }

            // TIPO DE SERVICIO (15 pts)
            const serviceSelect = document.getElementById('service_type');
            let serviceText = "";
            if (serviceSelect && serviceSelect.selectedIndex >= 0) {
                serviceText = serviceSelect.options[serviceSelect.selectedIndex].text.toUpperCase();
            }

            if (serviceText.includes("ESPECIALIZADOS")) {
                score += 15;
            } else if (serviceText.includes("PROEM")) {
                score += 10;
            } else if (serviceText !== "SELECCIONE..." && serviceText !== "") {
                score += 5;
            }

            // SECTOR / INDUSTRIA (5 pts)
            const macroId = document.getElementById('macro_category').value; 
            if(macroId !== '' && historicalMacro.hasOwnProperty(macroId)) {
                if(historicalMacro[macroId] > 50) score += 5;
                else score += 3;
            } else if (macroId !== '') { 
                score += 2; 
            }

            // ESTADO / UBICACIÓN (5 pts)
            const stateSelector = document.getElementById('state_selector');
            const stateId = stateSelector ? stateSelector.value : '';
            if(stateId !== '' && historicalState.hasOwnProperty(stateId)) {
                if(historicalState[stateId] > 50) score += 5;
                else score += 3;
            } else if (stateId !== '') { 
                score += 2; 
            }

            // COMPLEJIDAD (5 pts)
            const complexityInput = document.querySelector('[name="complexity_level"]');
            const complexity = complexityInput ? parseInt(complexityInput.value) : 3;

            if(complexity === 1) score += 5;
            else if(complexity === 2) score += 4;
            else if(complexity === 3) score += 3;
            else if(complexity === 4) score += 2;
            else if(complexity === 5) score += 1;
            
            score = Math.round(score);
            if(score > 100) score = 100;

            updateUI(score);
        }

        function updateUI(score) {
             const bar = document.getElementById('viabilityBar');
             const textClasif = document.getElementById('classificationText');
             const textScore = document.getElementById('scoreText');
             const card = document.getElementById('viabilityCard');
             const hiddenInput = document.getElementById('classification_hidden');

             let grupo = "D";
             let colorClass = "bg-secondary";
             let borderColor = "#6c757d"; 

             if (score >= 80) { 
                 grupo = "A"; colorClass = "bg-success"; borderColor = "#198754"; 
             } else if (score >= 60) { 
                 grupo = "B"; colorClass = "bg-primary"; borderColor = "#0d6efd"; 
             } else if (score >= 40) { 
                 grupo = "C"; colorClass = "bg-warning text-dark"; borderColor = "#ffc107"; 
             } else { 
                 grupo = "D"; colorClass = "bg-danger"; borderColor = "#dc3545"; 
             }

             if(bar) {
                 bar.style.width = score + "%";
                 bar.className = `progress-bar ${colorClass}`;
             }
             if(textClasif) {
                 textClasif.innerText = `GRUPO ${grupo}`;
                 textClasif.style.color = borderColor;
             }
             if(textScore) textScore.innerText = `${score} / 100 pts`;
             if(card) card.style.borderLeftColor = borderColor;
             if(hiddenInput) hiddenInput.value = grupo;
        }

        // ----------------------------------------------------
        // 2. GESTIÓN DE CAMPOS OBLIGATORIOS Y ASISTENTES DE CITA
        // ----------------------------------------------------
        const pipelineSelect = document.getElementById('pipeline_stage');
        const divDatosCita = document.getElementById('div_datos_cita');
        
        const requiredCitaInputs = [
            document.getElementById('cita_company_name'),
            document.getElementById('cita_giro'),
            document.getElementById('cita_company_size'),
            document.getElementById('cita_attendee_name'),
            document.getElementById('cita_attendee_position'),
            document.getElementById('cita_context')
        ];

        function toggleDatosCita() {
            if (!pipelineSelect || !divDatosCita) return;

            if (pipelineSelect.value === 'Contactado' || pipelineSelect.value === 'Contactado Pendiente') {
                divDatosCita.classList.remove('d-none');
                requiredCitaInputs.forEach(input => {
                    if (input) input.setAttribute('required', 'required');
                });
            } else {
                divDatosCita.classList.add('d-none');
                requiredCitaInputs.forEach(input => {
                    if (input) input.removeAttribute('required');
                });
            }
        }

        if (pipelineSelect) {
            pipelineSelect.addEventListener('change', toggleDatosCita);
            toggleDatosCita(); // Comprobar estado inicial al cargar
        }

    });
</script>

<?= $this->endSection() ?>