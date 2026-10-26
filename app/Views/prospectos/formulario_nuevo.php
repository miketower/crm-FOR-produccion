<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

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

<div class="card shadow-lg border-0">
    <div class="card-body p-4">
        
        <form action="<?= base_url('prospectos/guardar') ?>" method="post" id="prospectForm">
            <?= csrf_field() ?>

            <h5 class="section-title mt-0">1. Datos Generales de la Empresa</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre de la Empresa (Cliente)</label>
                    <input type="text" name="company_name" class="form-control" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Sector / Categoría</label>
                    <select name="industry_id" id="macro_category" class="form-select calc-trigger" required>
                        <option value="">Seleccione...</option>
                        <?php if(!empty($categorias)): ?>
                            <?php foreach($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['nombre_categoria']) ?></option>
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
                                <option value="<?= $s['id'] ?>" ... ><?= esc($s['name']) ?></option>
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
                                <option value="<?= $tv['id'] ?>"><?= esc($tv['nombre']) ?></option>
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
                                <option value="<?= $socio['id'] ?>"><?= esc($socio['nombre']) ?></option>
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
                                <option value="<?= $edo['id'] ?>" ... >
                                    <?= esc($edo['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Número de Empleados</label>
                    <input type="number" name="employees_count" class="form-control" placeholder="0">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Valor del Ticket ($)</label>
                    <input type="number" step="0.01" name="ticket_value" id="ticket" class="form-control calc-trigger" placeholder="0.00">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Complejidad (1-5)</label>
                    <select name="complexity_level" class="form-select calc-trigger">
                        <option value="3" selected>3 - Normal</option>
                        <option value="1">1 - Muy Simple</option>
                        <option value="2">2 - Simple</option>
                        <option value="4">4 - Complejo</option>
                        <option value="5">5 - Muy Complejo</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Duración (Meses)</label>
                    <input type="number" name="duration_months" class="form-control" placeholder="Ej. 12">
                </div>
            </div>

            <h5 class="section-title">2. Finanzas y Métricas RH</h5>
            <div class="bg-light p-3 rounded mb-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Ingreso Total Cobrado ($)</label>
                        <input type="number" step="0.01" name="total_income_charged" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-success">% Comisión</label>
                        <input type="number" step="0.01" name="commission_percent" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Costos Directos ($)</label>
                        <input type="number" step="0.01" name="direct_costs" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Costos Indirectos ($)</label>
                        <input type="number" step="0.01" name="indirect_costs" class="form-control">
                    </div>
                </div>
            </div>

            <h5 class="section-title">3. Mapa Político y Decisión</h5>
            <?php 
                $roles = ['Dueño/Socio', 'CFO', 'Líder Admin/Finanzas', 'Dir. General', 'Contador', 'Líder RH', 'Otro']; 
            ?>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">1. Primer Contacto</label>
                    <select name="first_contact_role" class="form-select form-select-sm">
                        <option value="">-- Seleccione --</option>
                        <?php foreach($roles as $r): ?><option value="<?= $r ?>"><?= $r ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">4. Quién Decidió</label>
                    <select name="decision_maker_role" id="decisor" class="form-select form-select-sm calc-trigger">
                        <option value="">-- Seleccione --</option>
                        <?php foreach($roles as $r): ?><option value="<?= $r ?>"><?= $r ?></option><?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold">Perfil Psicológico</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check calc-trigger" name="psychological_profile" id="p1" value="Conservador" checked>
                        <label class="btn btn-outline-secondary" for="p1">Conservador</label>

                        <input type="radio" class="btn-check calc-trigger" name="psychological_profile" id="p2" value="Estrategico">
                        <label class="btn btn-outline-secondary" for="p2">Estratégico</label>
                    </div>
                </div>
            </div>

            <h5 class="section-title mt-4">4. Checklist Cualitativo</h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <textarea name="general_observations" class="form-control" rows="2" placeholder="Observaciones generales..."></textarea>
                </div>
            </div>

            <div class="d-grid gap-2 mt-5 mb-5">
                <button type="submit" class="btn btn-primary btn-lg shadow">💾 Guardar Prospecto</button>
            </div>
            <input type="hidden" name="classification_group" id="classification_hidden" value="D">
        </form>
    </div>
</div>

<script>
    // Variables de PHP (Historial)
    // Nota: Aunque Victoria pidió hardcodear el servicio, mantenemos las variables 
    // por si en la Fase 2 queremos volver a activar la inteligencia histórica.
    const historicalMacro   = <?= json_encode($pesosMacro ?? []) ?>;
    const historicalState   = <?= json_encode($pesosEstado ?? []) ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionamos todos los inputs que afectan el cálculo
        const allInputs = document.querySelectorAll('.calc-trigger, select, input'); 
        allInputs.forEach(input => {
            input.addEventListener('change', calculateViability);
            input.addEventListener('input', calculateViability);
        });

        calculateViability(); // Ejecutar al inicio

        function calculateViability() {
            let score = 0;

            // ------------------------------------------------
            // 1. TICKET PROMEDIO (El Rey 👑 - 70 pts)
            // ------------------------------------------------
            const ticketInput = parseFloat(document.getElementById('ticket').value) || 0;
            const metaTicket = 1500000; // 1.5 Millones

            if (ticketInput >= metaTicket) {
                score += 70; // Si llega a la meta, se lleva todo el pastel
            } else {
                // Regla de tres: Si trae 750,000, se lleva 35 pts.
                score += (ticketInput / metaTicket) * 70;
            }

            // ------------------------------------------------
            // 2. TIPO DE SERVICIO (Hardcodeado - 15 pts) 🛠️
            // ------------------------------------------------
            // Buscamos el texto de la opción seleccionada, no el ID
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
                score += 5; // Para "Otros" o cualquier otro servicio nuevo
            }

            // ------------------------------------------------
            // 3. SECTOR / INDUSTRIA (5 pts) 🏭
            // ------------------------------------------------
            const macroId = document.getElementById('macro_category').value; 
            // Si hay dato histórico positivo, damos los 5 pts, si no, 1 pto base
            if(macroId !== '' && historicalMacro.hasOwnProperty(macroId)) {
                // Usamos el histórico solo para validar si es "bueno" (>50% winrate)
                if(historicalMacro[macroId] > 50) score += 5;
                else score += 3;
            } else if (macroId !== '') { 
                score += 2; 
            }

            // ------------------------------------------------
            // 4. ESTADO / UBICACIÓN (5 pts) 📍
            // ------------------------------------------------
            const stateSelector = document.getElementById('state_selector');
            const stateId = stateSelector ? stateSelector.value : '';
            
            if(stateId !== '' && historicalState.hasOwnProperty(stateId)) {
                if(historicalState[stateId] > 50) score += 5;
                else score += 3;
            } else if (stateId !== '') { 
                score += 2; 
            }

            // ------------------------------------------------
            // 5. COMPLEJIDAD (5 pts) 🧩
            // ------------------------------------------------
            // Ojo: Aquí asumimos que "Menos complejo" es mejor
            // 1 (Muy simple) = 5 pts
            // 3 (Normal) = 3 pts
            // 5 (Muy complejo) = 1 pt
            
            // Necesitamos buscar el input de complejidad. 
            // Si le pusiste name="complexity_level", lo buscamos así:
            const complexityInput = document.querySelector('[name="complexity_level"]');
            const complexity = complexityInput ? parseInt(complexityInput.value) : 3;

            if(complexity === 1) score += 5;
            else if(complexity === 2) score += 4;
            else if(complexity === 3) score += 3;
            else if(complexity === 4) score += 2;
            else if(complexity === 5) score += 1;
            
            // Redondear final
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

             // LÓGICA DE GRUPOS (Ajustada para ser exigente)
             if (score >= 80) { 
                 grupo = "A"; 
                 colorClass = "bg-success"; 
                 borderColor = "#198754"; 
             } else if (score >= 60) { 
                 grupo = "B"; 
                 colorClass = "bg-primary"; 
                 borderColor = "#0d6efd"; 
             } else if (score >= 40) { 
                 grupo = "C"; 
                 colorClass = "bg-warning text-dark"; 
                 borderColor = "#ffc107"; 
             } else { 
                 grupo = "D"; 
                 colorClass = "bg-danger"; 
                 borderColor = "#dc3545"; 
             }

             // Actualizar Visuales
             bar.style.width = score + "%";
             bar.className = `progress-bar ${colorClass}`;
             
             // Validación por si el elemento existe (en editar a veces cambia el ID)
             if(textClasif) {
                 textClasif.innerText = `GRUPO ${grupo}`;
                 textClasif.style.color = borderColor;
             }
             
             if(textScore) textScore.innerText = `${score} / 100 pts`;
             if(card) card.style.borderLeftColor = borderColor;

             // Guardar en Input Oculto
             if(hiddenInput) hiddenInput.value = grupo;
        }
    });
</script>

<?= $this->endSection() ?>