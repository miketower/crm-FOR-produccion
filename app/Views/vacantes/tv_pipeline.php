<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOR Human Capital - Vacantes TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #0d1117 !important; 
            color: #ffffff !important; 
            overflow-y: auto; 
            padding: 10px 15px; 
            font-family: system-ui, -apple-system, sans-serif; 
        }

        .card-tv { 
            background-color: #161b22 !important; 
            border: 1px solid #30363d !important; 
            border-radius: 6px; 
            color: #ffffff !important; 
        }

        .text-light-custom { color: #e6edf3 !important; }
        .text-muted-custom { color: #a3b1c2 !important; }
        .text-gold { color: #f2cc60 !important; }

        .badge-activa { background-color: #238636; color: #ffffff; font-size: 0.7rem; }
        .badge-pausada { background-color: #6e7681; color: #ffffff; font-size: 0.7rem; }
        .badge-cerrada { background-color: #da3633; color: #ffffff; font-size: 0.7rem; }

        .progress { background-color: #21262d; border-radius: 3px; }
        
        /* Círculos del Stepper reducidos */
        .step-circle { 
            width: 20px; 
            height: 20px; 
            font-size: 0.65rem; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: bold; 
            margin: 0 auto; 
        }
        
        .step-inactive { 
            background-color: #21262d; 
            color: #8b949e; 
            border: 1px solid #30363d; 
        }
    </style>
</head>
<body>

<!-- Header Compacto -->
<div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-secondary pb-2">
    <div class="d-flex align-items-center gap-2">
        <h4 class="fw-bold text-white mb-0"><i class="bi bi-tv me-2 text-success"></i>CONTROL DE VACANTES</h4>
        <span class="badge bg-success" style="font-size: 0.75rem;"><i class="bi bi-broadcast me-1"></i> EN VIVO</span>
    </div>
    <div class="text-end">
        <small class="text-muted-custom" id="last-update" style="font-size: 0.75rem;">Sincronizando...</small>
    </div>
</div>

<div class="d-flex flex-column gap-2" id="vacantes-tv-container">
    <!-- Renderizado dinámico -->
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
const etapasNombres = [
    'Abierta', 'Búsqueda', 'Entrevistas FOR', 'Bloque', 
    'Entrevista Cliente', 'Definición Finalista', 'Propuesta/Contrato', 'Facturado'
];

function getGradientColor(percentage) {
    let hue = (percentage * 1.2); 
    return `hsl(${hue}, 80%, 45%)`;
}

function cargarVacantesTV() {
    $.getJSON('<?= base_url("vacantes/getVacantesJson") ?>', function(data) {
        let html = '';

        if (!data || data.length === 0) {
            $('#vacantes-tv-container').html('<div class="text-center text-muted-custom py-4 fs-5">No hay vacantes registradas.</div>');
            return;
        }

        data.forEach(v => {
            const etapaActual = parseInt(v.etapa_num || 1);
            const porcentaje = Math.round((etapaActual / 8) * 100);
            const colorBarra = getGradientColor(porcentaje);

            // Estatus Badge Ultra Compacto
            let badgeEstatus = '<span class="badge badge-activa"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Activa</span>';
            if (v.estatus === 'Pausada') badgeEstatus = '<span class="badge badge-pausada"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Pausada</span>';
            if (v.estatus === 'Cerrada') badgeEstatus = '<span class="badge badge-cerrada"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Cerrada</span>';

            // Stepper
            let stepperHtml = '';
            etapasNombres.forEach((nombre, index) => {
                const pasoNum = index + 1;
                const estaCompletado = pasoNum <= etapaActual;
                const esActual = pasoNum === etapaActual;

                stepperHtml += `
                    <div class="text-center flex-fill position-relative">
                        <div class="step-circle ${estaCompletado ? '' : 'step-inactive'}" 
                             style="background-color: ${estaCompletado ? colorBarra : ''}; color: ${estaCompletado ? '#ffffff' : ''};">
                            ${pasoNum}
                        </div>
                        <span class="d-block text-truncate mt-1 ${esActual ? 'fw-bold text-white' : 'text-muted-custom'}" style="font-size: 0.62rem; max-width: 80px;">
                            ${nombre}
                        </span>
                    </div>
                `;
            });

            html += `
                <div class="card card-tv py-2 px-3 shadow-sm">
                    <div class="row align-items-center g-2">
                        <!-- IZQUIERDA: DETALLES COMPACTOS DE LA VACANTE -->
                        <div class="col-lg-4 border-end border-secondary pe-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold text-gold mb-0 text-uppercase text-truncate" style="max-width: 70%; font-size: 0.95rem;">
                                    ${v.titulo_vacante || 'VACANTE'}
                                </h6>
                                ${badgeEstatus}
                            </div>
                            <div class="text-light-custom text-truncate mb-1" style="font-size: 0.82rem;">
                                <i class="bi bi-building me-1 text-muted-custom"></i><strong class="text-white">${v.empresa_cliente || 'N/A'}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted-custom" style="font-size: 0.72rem;">
                                <span><i class="bi bi-person-badge me-1"></i><strong class="text-white">${v.consultor || 'Neto'}</strong></span>
                                <span><i class="bi bi-calendar3 me-1"></i>${v.fecha_apertura || 'S/F'}</span>
                            </div>
                        </div>

                        <!-- DERECHA: BARRA Y STEPPER -->
                        <div class="col-lg-8 ps-lg-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-uppercase" style="color: ${colorBarra}; font-size: 0.78rem;">
                                    Etapa ${etapaActual}: ${etapasNombres[etapaActual - 1]}
                                </span>
                                <span class="fw-bold" style="color: ${colorBarra}; font-size: 0.88rem;">${porcentaje}%</span>
                            </div>
                            
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: ${porcentaje}%; background-color: ${colorBarra}; transition: width 0.5s;"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                ${stepperHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#vacantes-tv-container').html(html);
        $('#last-update').text('Última sync: ' + new Date().toLocaleTimeString());
    });
}

$(document).ready(function() {
    cargarVacantesTV();
    setInterval(cargarVacantesTV, 8000);
});
</script>
</body>
</html>