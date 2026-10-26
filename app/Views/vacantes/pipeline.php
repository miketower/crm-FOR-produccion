<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Control de Vacantes</h3>
        <span class="text-muted small">Listado general ordenado por la más reciente</span>
    </div>
    <a href="<?= base_url('vacantes/crear') ?>" class="btn btn-primary fw-bold">
        <i class="bi bi-plus-circle me-1"></i> Nueva Vacante
    </a>
</div>

<div class="d-flex flex-column gap-3" id="vacantes-container">
    <!-- Carga dinámica via JS -->
</div>

<!-- Modal para Editar Vacante -->
<div class="modal fade" id="modalEditarVacante" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Actualizar Vacante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarVacante">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estatus de la Vacante</label>
                        <select class="form-select" id="edit_estatus" name="estatus">
                            <option value="Activa">Activa (Verde)</option>
                            <option value="Pausada">Pausada (Gris)</option>
                            <option value="Cerrada">Cerrada (Rojo)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Consultor Asignado</label>
                        <select class="form-select" id="edit_consultor" name="consultor">
                            <option value="Neto">Neto</option>
                            <option value="Carlos">Carlos</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Etapa del Flujo</label>
                        <select class="form-select" id="edit_etapa_num" name="etapa_num">
                            <option value="1">1. Abierta</option>
                            <option value="2">2. Búsqueda</option>
                            <option value="3">3. Entrevistas FOR</option>
                            <option value="4">4. Bloque</option>
                            <option value="5">5. Entrevista Cliente</option>
                            <option value="6">6. Definición de Finalista</option>
                            <option value="7">7. Propuesta o Contrato</option>
                            <option value="8">8. Facturado</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success fw-bold" onclick="guardarCambiosVacante()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
const etapasNombres = [
    'Abierta', 'Búsqueda', 'Entrevistas FOR', 'Bloque', 
    'Entrevista Cliente', 'Definición Finalista', 'Propuesta/Contrato', 'Facturado'
];

function getGradientColor(percentage) {
    // Calcula la transición de color de rojo (0%) a amarillo (50%) a verde (100%)
    let hue = (percentage * 1.2); 
    return `hsl(${hue}, 80%, 45%)`;
}

function cargarVacantes() {
    $.getJSON('<?= base_url("vacantes/getVacantesJson") ?>', function(data) {
        let html = '';

        data.forEach(v => {
            const etapaActual = parseInt(v.etapa_num || 1);
            const porcentaje = Math.round((etapaActual / 8) * 100);
            const colorBarra = getGradientColor(porcentaje);

            // Estatus Badge
            let badgeEstatus = '<span class="badge bg-success"><i class="bi bi-circle-fill me-1 small"></i>Activa</span>';
            if (v.estatus === 'Pausada') badgeEstatus = '<span class="badge bg-secondary"><i class="bi bi-circle-fill me-1 small"></i>Pausada</span>';
            if (v.estatus === 'Cerrada') badgeEstatus = '<span class="badge bg-danger"><i class="bi bi-circle-fill me-1 small"></i>Cerrada</span>';

            // Generador de pasos del Stepper
            let stepperHtml = '';
            etapasNombres.forEach((nombre, index) => {
                const pasoNum = index + 1;
                const estaCompletado = pasoNum <= etapaActual;
                const esActual = pasoNum === etapaActual;

                stepperHtml += `
                    <div class="text-center flex-fill position-relative">
                        <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold ${estaCompletado ? 'text-white' : 'text-muted bg-light border'}" 
                             style="width:26px; height:26px; font-size:0.75rem; background-color: ${estaCompletado ? colorBarra : ''};">
                            ${pasoNum}
                        </div>
                        <span class="d-block text-truncate mt-1 ${esActual ? 'fw-bold text-dark' : 'text-muted'}" style="font-size: 0.65rem; max-width: 75px;">
                            ${nombre}
                        </span>
                    </div>
                `;
            });

            html += `
                <div class="card border-0 shadow-sm p-3">
                    <div class="row align-items-center">
                        <!-- LADO IZQUIERDO: DETALLES DE LA VACANTE -->
                        <div class="col-lg-4 border-end">
                            <div class="d-flex justify-content-between align-items-start me-2">
                                <h5 class="fw-bold text-primary mb-1">${v.titulo_vacante}</h5>
                                ${badgeEstatus}
                            </div>
                            <div class="text-secondary small mb-1"><i class="bi bi-building me-1"></i><strong>Cliente:</strong> ${v.empresa_cliente}</div>
                            <div class="d-flex gap-3 text-muted small mt-2">
                                <span><i class="bi bi-person-badge me-1"></i><strong>Consultor:</strong> ${v.consultor || 'Neto'}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>${v.fecha_apertura}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary mt-2" onclick='abrirModalEditar(${JSON.stringify(v)})'>
                                <i class="bi bi-pencil me-1"></i>Editar Estatus/Etapa
                            </button>
                        </div>

                        <!-- LADO DERECHO: BARRA Y STEPPER DE PROGRESO -->
                        <div class="col-lg-8 ps-lg-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-uppercase" style="color: ${colorBarra}">
                                    Etapa ${etapaActual}: ${etapasNombres[etapaActual - 1]}
                                </span>
                                <span class="fw-bold small">${porcentaje}%</span>
                            </div>
                            
                            <!-- Barra de Progreso Contigua -->
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar transition-all" role="progressbar" 
                                     style="width: ${porcentaje}%; background-color: ${colorBarra};"></div>
                            </div>

                            <!-- Stepper de Pasos -->
                            <div class="d-flex justify-content-between align-items-center">
                                ${stepperHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#vacantes-container').html(html);
    });
}

function abrirModalEditar(vacante) {
    $('#edit_id').val(vacante.id);
    $('#edit_estatus').val(vacante.estatus || 'Activa');
    $('#edit_consultor').val(vacante.consultor || 'Neto');
    $('#edit_etapa_num').val(vacante.etapa_num || 1);
    $('#modalEditarVacante').modal('show');
}

function guardarCambiosVacante() {
    $.post('<?= base_url("vacantes/actualizar") ?>', $('#formEditarVacante').serialize(), function(res) {
        if (res.status === 'success') {
            $('#modalEditarVacante').modal('hide');
            cargarVacantes();
        }
    });
}

$(document).ready(function() {
    cargarVacantes();
});
</script>
<?= $this->endSection() ?>