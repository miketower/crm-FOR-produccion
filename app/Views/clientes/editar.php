<?= $this->extend('layout/main') ?>

<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-0">Expediente Comercial: <?= esc($cliente['company_name']) ?></h4>
        <small class="text-muted">Completa los datos del formato de alta para operación y contratos.</small>
    </div>
    <a href="<?= base_url('clientes') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Regresar a Cartera
    </a>
</div>

<div class="card shadow border-0">
    <div class="card-header bg-white pt-3 pb-0 border-0">
        <!-- Navegación por Pestañas del Excel -->
        <ul class="nav nav-tabs card-header-tabs" id="clienteTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-uppercase small" id="generales-tab" data-bs-toggle="tab" data-bs-target="#generales" type="button" role="tab"><i class="bi bi-person-vcard-fill me-2"></i>1. Datos del Cliente</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-uppercase small" id="operacion-tab" data-bs-toggle="tab" data-bs-target="#operacion" type="button" role="tab"><i class="bi bi-gear-wide-connected me-2"></i>2. Datos para Operación</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-uppercase small" id="comercial-tab" data-bs-toggle="tab" data-bs-target="#comercial" type="button" role="tab"><i class="bi bi-currency-dollar me-2"></i>3. Alta Comercial</button>
            </li>
        </ul>
    </div>
    
    <form action="<?= base_url('clientes/actualizar/' . $cliente['id']) ?>" method="POST">
        <div class="card-body p-4">
            <div class="tab-content" id="clienteTabContent">
                
                <!-- PESTAÑA 1: DATOS DEL CLIENTE -->
                <div class="tab-pane fade show active" id="generales" role="tabpanel">
                    <h5 class="fw-bold text-primary mb-3 pb-2 border-bottom">Información de Identificación Fiscal</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Origen del Prospecto</label>
                            <input type="text" name="origen_prospecto" class="form-control form-control-sm" value="<?= esc($cliente['origen_prospecto'] ?? '') ?>" placeholder="Ej. Recomendado, Redes...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Fecha de Alta de Formato</label>
                            <input type="date" name="fecha_alta_formato" class="form-control form-control-sm" value="<?= $cliente['fecha_alta_formato'] ?? date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Tipo de Persona</label>
                            <select name="tipo_persona" class="form-select form-select-sm">
                                <option value="Moral" <?= ($cliente['tipo_persona'] ?? 'Moral') == 'Moral' ? 'selected' : '' ?>>Persona Moral</option>
                                <option value="Física" <?= ($cliente['tipo_persona'] ?? '') == 'Física' ? 'selected' : '' ?>>Persona Física</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small">Nombre o Razón Social</label>
                            <input type="text" name="company_name" class="form-control form-control-sm fw-bold" value="<?= esc($cliente['company_name']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">RFC</label>
                            <input type="text" name="rfc" class="form-control form-control-sm font-monospace fw-bold" value="<?= esc($cliente['rfc'] ?? '') ?>" placeholder="XAXX010101000" maxlength="13">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Principal Actividad del Cliente</label>
                            <textarea name="actividad_principal" class="form-control form-control-sm" rows="2"><?= esc($cliente['actividad_principal'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Domicilio Fiscal (COMPLETO)</label>
                            <textarea name="domicilio_fiscal" class="form-control form-control-sm" rows="2"><?= esc($cliente['domicilio_fiscal'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mt-4 mb-3 pb-2 border-bottom">Contacto y Envío de Documentos</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Nombre de Contacto</label>
                            <input type="text" name="nombre_contacto" class="form-control form-control-sm" value="<?= esc($cliente['nombre_contacto'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Teléfono de Contacto</label>
                            <input type="text" name="telefono_contacto" class="form-control form-control-sm" value="<?= esc($cliente['telefono_contacto'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" class="form-control form-control-sm" value="<?= esc($cliente['correo_electronico'] ?? '') ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small">Domicilio para Envío de Documentación</label>
                            <input type="text" name="domicilio_envio_docs" class="form-control form-control-sm" value="<?= esc($cliente['domicilio_envio_docs'] ?? '') ?>" placeholder="Dejar vacío si es el mismo fiscal">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Nombre de Quien Recibe Documentos</label>
                            <input type="text" name="nombre_recibe_docs" class="form-control form-control-sm" value="<?= esc($cliente['nombre_recibe_docs'] ?? '') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small d-block">Servicios Contratados</label>
                            <?php 
                                $servicios_db = explode(',', $cliente['servicios_contratados'] ?? ''); 
                                $opciones = ['SOLUCIONES','PROEM', 'ESPECIALIZADOS', 'PIC', 'SINDICATOS'];
                                foreach($opciones as $opc):
                            ?>
                                <div class="form-check form-check-inline me-4">
                                    <input class="form-check-input" type="checkbox" name="servicios_contratados[]" value="<?= $opc ?>" id="srv_<?= $opc ?>" <?= in_array($opc, $servicios_db) ? 'checked' : '' ?>>
                                    <label class="form-check-input-label fw-bold text-dark small" for="srv_<?= $opc ?>"><?= $opc ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 2: DATOS PARA OPERACIÓN -->
                <div class="tab-pane fade" id="operacion" role="tabpanel">
                    <h5 class="fw-bold text-success mb-3 pb-2 border-bottom">Esquema Operativo Financiero</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Esquema o Servicio PROEM (Detalle)</label>
                            <textarea name="esquema_servicio_proem" class="form-control form-control-sm" rows="2" placeholder="Describa el alcance pactado..."><?= esc($cliente['esquema_servicio_proem'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Fecha Tentativa de Inicio de Operación</label>
                            <input type="date" name="fecha_inicio_operacion" class="form-control form-control-sm" value="<?= $cliente['fecha_inicio_operacion'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Condiciones del IVA</label>
                            <input type="text" name="condiciones_iva" class="form-control form-control-sm" value="<?= esc($cliente['condiciones_iva'] ?? '') ?>" placeholder="Ej. IVA 16%, Exento...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Monto Tentativo de Operación ($)</label>
                            <input type="number" step="0.01" name="monto_tentativo_operacion" class="form-control form-control-sm fw-bold text-success" value="<?= $cliente['monto_tentativo_operacion'] ?? '0.00' ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small">Comisión a Cobrar al Cliente y Sobre Qué Base</label>
                            <textarea name="comision_base_cliente" class="form-control form-control-sm" rows="2" placeholder="Ej. 5% sobre nómina bruta..."><?= esc($cliente['comision_base_cliente'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <h5 class="fw-bold text-success mt-4 mb-3 pb-2 border-bottom">Datos de Remuneración y Retorno</h5>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small">Persona(as) a Quien se Remunera</label>
                            <input type="text" name="personas_remunera" class="form-control form-control-sm" value="<?= esc($cliente['personas_remunera'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Profesión</label>
                            <input type="text" name="profesion" class="form-control form-control-sm" value="<?= esc($cliente['profesion'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">¿Tiene Ingresos por Otras Actividades Económicas?</label>
                            <select name="ingresos_otras_actividades" class="form-select form-select-sm">
                                <option value="No" <?= ($cliente['ingresos_otras_actividades'] ?? 'No') == 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Sí" <?= ($cliente['ingresos_otras_actividades'] ?? '') == 'Sí' ? 'selected' : '' ?>>Sí</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small">¿Cuáles?</label>
                            <input type="text" name="cuales_otras_actividades" class="form-control form-control-sm" value="<?= esc($cliente['cuales_otras_actividades'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Banco</label>
                            <input type="text" name="banco" class="form-control form-control-sm" value="<?= esc($cliente['banco'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Clabe Interbancaria (18 dígitos)</label>
                            <input type="text" name="clabe_interbancaria" class="form-control form-control-sm font-monospace" value="<?= esc($cliente['clabe_interbancaria'] ?? '') ?>" maxlength="18">
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 3: ALTA COMERCIAL (SOCIOS Y PORCENTAJES EDITABLES) -->
                <div class="tab-pane fade" id="comercial" role="tabpanel">
    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-warning mb-0">Esquemas de Comisiones y Redes Internas</h5>
                        
                        <?php if (session()->get('id') == 1): ?>
                            <div class="form-check form-switch bg-warning bg-opacity-10 px-4 py-2 rounded border border-warning">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="informacion_validada" value="1" id="switchValidar" <?= ($cliente['informacion_validada'] ?? 0) == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold text-dark small" for="switchValidar">
                                    <i class="bi bi-shield-lock-fill"></i> Autorizar y Bloquear Comisiones
                                </label>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="informacion_validada" value="<?= $cliente['informacion_validada'] ?? 0 ?>">
                        <?php endif; ?>
                    </div>

                    <?php 
                        // Determinamos si los campos deben bloquearse
                        // Se bloquean si ya está validado en la BD
                        $comisionesBloqueadas = (($cliente['informacion_validada'] ?? 0) == 1);
                    ?>

                    <?php if ($comisionesBloqueadas): ?>
                        <div class="alert alert-danger py-2 px-3 small border-0 mb-3 d-flex align-items-center">
                            <i class="bi bi-lock-fill fs-5 me-2"></i>
                            <span><strong>Comisiones Bloqueadas:</strong> Esta información ya fue validada y autorizada por Dirección. Los campos no pueden modificarse.</span>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small">El sistema calcula los porcentajes en automático, pero puedes modificarlos manualmente si existe un trato especial para este contrato.</p>
                    <?php endif; ?>
                    
                    <div class="row g-3 mb-4">
                        
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded border">
                                <label class="form-label fw-bold text-dark small"><i class="bi bi-person-fill text-primary"></i> Comisionista Involucrado</label>
                                <select name="comisionista_id" class="form-select form-select-sm fw-bold mb-2" <?= $comisionesBloqueadas ? 'disabled' : '' ?>>
                                    <option value="">-- Seleccione un Socio --</option>
                                    <?php foreach($socios as $socio): ?>
                                        <option value="<?= $socio['id'] ?>" <?= ($cliente['comisionista_id'] ?? '') == $socio['id'] ? 'selected' : '' ?>>
                                            <?= esc($socio['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted small fw-bold">% Comisión</span>
                                    <input type="number" step="0.01" min="0" max="100" name="pct_comisionista_asignado" class="form-control fw-bold text-primary" value="<?= $cliente['pct_comisionista_asignado'] ?? '0.00' ?>" <?= $comisionesBloqueadas ? 'disabled' : '' ?>>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded border">
                                <label class="form-label fw-bold text-dark small"><i class="bi bi-diagram-3-fill text-success"></i> Red Perteneciente</label>
                                <select name="red_socio_id" class="form-select form-select-sm fw-bold mb-2" <?= $comisionesBloqueadas ? 'disabled' : '' ?>>
                                    <option value="">-- Seleccione una Red --</option>
                                    <?php foreach($socios as $socio): ?>
                                        <option value="<?= $socio['id'] ?>" <?= ($cliente['red_socio_id'] ?? '') == $socio['id'] ? 'selected' : '' ?>>
                                            <?= esc($socio['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted small fw-bold">% Red</span>
                                    <input type="number" step="0.01" min="0" max="100" name="pct_red_asignado" class="form-control fw-bold text-success" value="<?= $cliente['pct_red_asignado'] ?? '0.00' ?>" <?= $comisionesBloqueadas ? 'disabled' : '' ?>>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded border">
                                <label class="form-label fw-bold text-dark small"><i class="bi bi-arrow-repeat text-warning"></i> Comisionista Seguimiento</label>
                                <select name="comisionista_seguimiento_id" class="form-select form-select-sm fw-bold mb-2" <?= $comisionesBloqueadas ? 'disabled' : '' ?>>
                                    <option value="">-- Seleccione un Socio --</option>
                                    <?php foreach($socios as $socio): ?>
                                        <option value="<?= $socio['id'] ?>" <?= ($cliente['comisionista_seguimiento_id'] ?? '') == $socio['id'] ? 'selected' : '' ?>>
                                            <?= esc($socio['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted small fw-bold">% Seguimiento</span>
                                    <input type="number" step="0.01" min="0" max="100" name="pct_seguimiento_asignado" class="form-control fw-bold text-warning" value="<?= $cliente['pct_seguimiento_asignado'] ?? '0.00' ?>" <?= $comisionesBloqueadas ? 'disabled' : '' ?>>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <div class="card-footer bg-light p-3 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-success fw-bold shadow-sm px-4">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Guardar Expediente de Cliente
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>