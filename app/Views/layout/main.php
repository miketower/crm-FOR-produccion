<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM FOR - Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    
    <style>
        body { background-color: #f4f6f9; overflow-x: hidden; }
        
        .sidebar { 
            min-width: 250px; 
            max-width: 250px; 
            height: 100vh; 
            overflow-y: auto; 
            background-color: #343a40; 
            color: #fff; 
            transition: all 0.3s; 
            position: fixed; 
            left: 0; 
            top: 0; 
            z-index: 1000; 
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #343a40;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #4b545c;
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #0d6efd;
        }

        .sidebar .brand { padding: 15px 20px; border-bottom: 1px solid #4b545c; display: block; text-align: center; }
        .logo-img { max-height: 45px; width: auto; }
        .sidebar ul.components { padding: 20px 0; }
        .sidebar ul li a { padding: 12px 20px; font-size: 0.95rem; display: block; color: #c2c7d0; text-decoration: none; border-left: 3px solid transparent; }
        .sidebar ul li a:hover, .sidebar ul li a.active { color: #fff; background: #494e53; border-left: 3px solid #0d6efd; }
        .sidebar ul li a i { margin-right: 10px; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); transition: all 0.3s; }
        .top-navbar { background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,.08); padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-avatar { width: 35px; height: 35px; background-color: #0d6efd; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        @media (max-width: 768px) { .sidebar { margin-left: -250px; } .sidebar.active { margin-left: 0; } .main-content { margin-left: 0; width: 100%; } }
        
        .notif-vencida { border-left: 3px solid #dc3545 !important; }
    </style>
</head>
<body>

<?php 
    $db = \Config\Database::connect();
    $userIdSesion = session()->get('id');

    // Consultar notificaciones sin leer de la BD para el usuario en sesión
    $misNotificaciones = [];
    if ($userIdSesion) {
        $misNotificaciones = $db->table('notificaciones')
                                ->where('user_id', $userIdSesion)
                                ->where('leido', 0)
                                ->orderBy('created_at', 'DESC')
                                ->get()
                                ->getResultArray();
    }

    $userEmail = session()->get('email');
    $userRole  = session()->get('role');
    
    // Identificadores de usuarios con acceso restricto/especial
    $esLoyda = ($userEmail === 'lberlangazambrano@gmail.com');
    $esVictoriaAdmin = ($userRole === 'admin' || $userEmail === 'admin@forhuman.com' || $userEmail === 'admin@victoria.com');

    $usuariosCaptura = [
        'danya.cruz@forhumancapital.mx',
        'samuel.nunez@theblancgroup.com',
        'alicia.gutierrez@forhumancapital.mx'
    ];
    $esCapturador = in_array($userEmail, $usuariosCaptura);

    $usuariosAsociados = [
        'aurora.esquivel@forhumancapital.mx',
        'yuridia.juarez@forhumancapital.mx'
    ];
    $esAsociados = in_array($userEmail, $usuariosAsociados);

    // Redirección de marca según el tipo de usuario
    $brandUrl = base_url('dashboard');
    if ($userRole === 'operaciones') {
        $brandUrl = base_url('operaciones');
    } elseif ($esLoyda) {
        $brandUrl = base_url('prospectos');
    } elseif ($userEmail === 'carlos.sarmiento@forhumancapital.mx') {
        $brandUrl = base_url('vacantes');
    } elseif ($esCapturador) {
        $brandUrl = base_url('carga-comercial');
    } elseif ($esAsociados) {
        $brandUrl = base_url('socios');
    } elseif ($userRole === 'events_rep') {
        $brandUrl = base_url('eventos');
    } elseif ($userRole === 'commercial_rep') {
        $brandUrl = base_url('dashboard/pipeline');
    }
?>

<nav class="sidebar">
    <a href="<?= $brandUrl ?>" class="brand text-center py-3">
        <img src="<?= base_url('assets/img/logo-white.png') ?>" alt="Logo CRM" class="logo-img img-fluid">
    </a>
    <ul class="list-unstyled components">
        
        <?php if ($userRole === 'operaciones'): ?>
            <!-- ========================================================== -->
            <!-- MENÚ EXCLUSIVO PARA ROL OPERACIONES (SOLO BITÁCORA) -->
            <!-- ========================================================== -->
            <li class="px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Mesa de Ayuda</li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'operaciones') !== false) ? 'active' : '' ?>" href="<?= base_url('operaciones') ?>">
                    <i class="bi bi-life-preserver me-2"></i> <span>Bitácora Operativa</span>
                </a>
            </li>

        <?php elseif ($esLoyda): ?>
            <!-- MENÚ EXCLUSIVO LOYDA BERLANGA (SOLO PERFILAMIENTO) -->
            <li class="px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Operación</li>
            <li class="nav-item">
                <a href="<?= base_url('prospectos') ?>" class="<?= (strpos(uri_string(), 'prospectos') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-list-task"></i> <span>Perfilamiento</span>
                </a>
            </li>

        <?php elseif ($userEmail === 'carlos.sarmiento@forhumancapital.mx'): ?>
            <!-- MENÚ EXCLUSIVO CARLOS SARMIENTO (SOLO VACANTES) -->
            <li class="px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Atracción de Talento</li>
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'vacantes') ? 'active' : '' ?>" href="<?= base_url('vacantes') ?>">
                    <i class="bi bi-kanban"></i> <span>Pipeline Vacantes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'vacantes/crear') !== false) ? 'active' : '' ?>" href="<?= base_url('vacantes/crear') ?>">
                    <i class="bi bi-plus-circle"></i> <span>Alta de Vacante</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('vacantes/tv') ?>" target="_blank">
                    <i class="bi bi-tv"></i> <span>Pantalla TV <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.7rem;"></i></span>
                </a>
            </li>

        <?php elseif ($esCapturador): ?>
            <!-- MENÚ RESTRINGIDO PARA CAPTURADORES -->
            <li class="px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Operación</li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'carga-comercial') !== false) ? 'active' : '' ?>" href="<?= base_url('carga-comercial') ?>">
                    <i class="bi bi-calendar2-week"></i> <span>Carga Semanal</span>
                </a>
            </li>

            <?php if ($userEmail === 'danya.cruz@forhumancapital.mx'): ?>
                <li class="mt-3 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Atracción de Talento</li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'vacantes') ? 'active' : '' ?>" href="<?= base_url('vacantes') ?>">
                        <i class="bi bi-kanban"></i> <span>Pipeline Vacantes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos(uri_string(), 'vacantes/crear') !== false) ? 'active' : '' ?>" href="<?= base_url('vacantes/crear') ?>">
                        <i class="bi bi-plus-circle"></i> <span>Alta de Vacante</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('vacantes/tv') ?>" target="_blank">
                        <i class="bi bi-tv"></i> <span>Pantalla TV <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.7rem;"></i></span>
                    </a>
                </li>

                <li class="mt-3 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Operación</li>
                <li>
                    <a href="<?= base_url('prospectos') ?>" class="<?= (strpos(uri_string(), 'prospectos') !== false) ? 'active' : '' ?>">
                        <i class="bi bi-list-task"></i> <span>Perfilamiento</span>
                    </a>
                </li>
            <?php endif; ?>

        <?php elseif ($userRole === 'events_rep'): ?>
            <!-- MENÚ EXCLUSIVO PARA EVENTOS (LUCIANA OSUNA) -->
            <li class="px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Gestión de Eventos</li>
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'eventos' || strpos(uri_string(), 'eventos/show') !== false) ? 'active' : '' ?>" href="<?= base_url('eventos') ?>">
                    <i class="bi bi-calendar2-event"></i> <span>Eventos</span>
                </a>
            </li>
            <li class="mt-3 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Operación</li>
            <li>
                <a href="<?= base_url('socios') ?>" class="<?= (uri_string() == 'socios') ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i> <span>Asociados FOR</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('socios/cierre-mes') ?>" class="<?= (strpos(uri_string(), 'cierre-mes') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check me-2"></i> <span>Cierre de Mes (Asociados)</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('prospectos') ?>" class="<?= (strpos(uri_string(), 'prospectos') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-list-task"></i> <span>Perfilamiento</span>
                </a>
            </li>

        <?php elseif ($esAsociados): ?>
            <!-- MENÚ EXCLUSIVO AURORA Y YURIDIA -->
            <li class="px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Operación</li>
            <li>
                <a href="<?= base_url('socios') ?>" class="<?= (uri_string() == 'socios') ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i> <span>Asociados FOR</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('socios/cierre-mes') ?>" class="<?= (strpos(uri_string(), 'cierre-mes') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check me-2"></i> <span>Cierre de Mes (Asociados)</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('prospectos') ?>" class="<?= (strpos(uri_string(), 'prospectos') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-list-task"></i> <span>Perfilamiento</span>
                </a>
            </li>

        <?php elseif ($userRole === 'commercial_rep'): ?>
            <!-- MENÚ EXCLUSIVO COMERCIAL (DAVID ZAPATA) -->
            <li class="px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Operación</li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'pipeline') !== false) ? 'active' : '' ?>" href="<?= base_url('dashboard/pipeline') ?>">
                    <i class="bi bi-kanban"></i> <span>Pipeline</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('prospectos') ?>" class="<?= (strpos(uri_string(), 'prospectos') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-list-task"></i> <span>Perfilamiento</span>
                </a>
            </li>

            <li class="mt-4 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Herramientas</li>
            <li>
                <a href="<?= base_url('calculadora') ?>" class="<?= (strpos(uri_string(), 'calculadora') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-calculator"></i> Calculadora Nómina
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (url_is('cotizador*')) ? 'active' : '' ?>" href="<?= base_url('cotizador') ?>">
                    <i class="bi bi-calculator"></i> <span>Cotizador</span>
                </a>
            </li>

        <?php else: ?>
            <!-- MENÚ COMPLETO PARA ADMINS Y RESTO DEL EQUIPO -->
            <li><a href="<?= base_url('dashboard') ?>" class="<?= uri_string() == 'dashboard' ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Inicio</a></li>
            
            <li class="mt-3 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Operación</li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'pipeline') !== false) ? 'active' : '' ?>" href="<?= base_url('dashboard/pipeline') ?>">
                    <i class="bi bi-kanban"></i> <span>Pipeline Ventas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'carga-comercial') !== false) ? 'active' : '' ?>" href="<?= base_url('carga-comercial') ?>">
                    <i class="bi bi-calendar2-week"></i> <span>Carga Semanal</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'eventos' || strpos(uri_string(), 'eventos/show') !== false) ? 'active' : '' ?>" href="<?= base_url('eventos') ?>">
                    <i class="bi bi-calendar-event"></i> <span>Eventos</span>
                </a>
            </li>
            <li><a href="<?= base_url('prospectos') ?>" class="<?= (strpos(uri_string(), 'prospectos') !== false) ? 'active' : '' ?>"><i class="bi bi-list-task"></i> Perfilamiento</a></li>
            
            <!-- BITÁCORA OPERATIVA -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'operaciones') !== false) ? 'active' : '' ?>" href="<?= base_url('operaciones') ?>">
                    <i class="bi bi-life-preserver me-2"></i>
                    <span>Bitácora Operativa</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (strpos(uri_string(), 'forecast') !== false) ? 'active' : '' ?>" href="<?= base_url('forecast') ?>">
                    <i class="bi bi-bullseye me-2"></i>
                    <span>Forecast</span>
                </a>
            </li>

            <!-- SECCIÓN VACANTES SOLO PARA ADMINISTRADORES -->
            <?php if ($userRole === 'admin'): ?>
                <li class="mt-3 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Atracción de Talento</li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'vacantes') ? 'active' : '' ?>" href="<?= base_url('vacantes') ?>">
                        <i class="bi bi-kanban me-2"></i> <span>Pipeline Vacantes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos(uri_string(), 'vacantes/crear') !== false) ? 'active' : '' ?>" href="<?= base_url('vacantes/crear') ?>">
                        <i class="bi bi-plus-circle me-2"></i> <span>Alta de Vacante</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('vacantes/tv') ?>" target="_blank">
                        <i class="bi bi-tv me-2"></i> <span>Pantalla TV <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.7rem;"></i></span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="mt-4 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Configuración</li>
            <?php if(session()->get('role') == 'admin'): ?>
                <li><a href="<?= base_url('usuarios') ?>" class="<?= uri_string() == 'usuarios' ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> Equipo / Usuarios</a></li>
            <?php endif; ?>

            <li><a href="<?= base_url('clientes') ?>"><i class="bi bi-building"></i> Clientes</a></li>
            <li><a href="<?= base_url('socios') ?>" class="<?= (uri_string() == 'socios') ? 'active' : '' ?>"><i class="bi bi-person-badge"></i> Asociados FOR</a></li>
            <!-- NUEVA OPCIÓN: CIERRE DE MES (ASOCIADOS) -->
            <li><a href="<?= base_url('socios/cierre-mes') ?>" class="<?= (strpos(uri_string(), 'cierre-mes') !== false) ? 'active' : '' ?>"><i class="bi bi-calendar-check me-2"></i> Cierre de Mes (Asociados)</a></li>
            
            <li><a href="<?= base_url('ventas') ?>"><i class="bi bi-cash-coin"></i> Ventas e Ingresos</a></li>
            <li><a href="<?= base_url('importar') ?>" class="<?= uri_string() == 'importar' ? 'active' : '' ?>"><i class="bi bi-cloud-upload"></i> Cierre Mensual</a></li>

            <!-- DIRECTORIO DE FORMULARIOS (SOLO VICTORIA Y ADMINS) -->
            <?php if ($esVictoriaAdmin): ?>
                <li class="mt-3 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Herramientas Web</li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos(uri_string(), 'directorio-formularios') !== false) ? 'active' : '' ?>" href="<?= base_url('directorio-formularios') ?>">
                        <i class="bi bi-journal-code me-2"></i>
                        <span>Directorio Formularios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos(uri_string(), 'salarios') !== false) ? 'active' : '' ?>" href="<?= base_url('salarios') ?>">
                        <i class="bi bi-journal-code me-2"></i>
                        <span>Guía Global de Salarios 2027</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="mt-3 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Herramientas Web</li>
            <?php if($userEmail === 'carlos.sarmiento@forhumancapital.mx'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos(uri_string(), 'salarios') !== false) ? 'active' : '' ?>" href="<?= base_url('salarios') ?>">
                        <i class="bi bi-journal-code me-2"></i>
                        <span>Guía Global de Salarios 2027</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="mt-4 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Estrategia</li>
            <li>
                <a href="<?= base_url('formularios/alineacion') ?>" class="<?= (strpos(uri_string(), 'alineacion') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-check"></i> Alineación Estratégica
                </a>
            </li>

            <li class="mt-4 px-3 text-uppercase small text-muted" style="font-size: 0.7rem;">Herramientas</li>
            <li>
                <a href="<?= base_url('calculadora') ?>" class="<?= (strpos(uri_string(), 'calculadora') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-calculator"></i> Calculadora Nómina
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (url_is('cotizador*')) ? 'active' : '' ?>" href="<?= base_url('cotizador') ?>">
                    <i class="bi bi-calculator"></i> <span>Cotizador</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<div class="main-content">
    <header class="top-navbar">
        <div class="d-flex align-items-center">
            <h5 class="m-0 text-secondary">Bienvenido, <?= session()->get('name') ?? 'Usuario' ?></h5>
        </div>

        <div class="d-flex align-items-center gap-4">
            <!-- DESPLEGABLE DE NOTIFICACIONES -->
            <div class="dropdown">
                <?php 
                    $totalNotifBD = count($misNotificaciones);
                    $totalTareas = isset($tareasPendientes) ? count($tareasPendientes) : 0;
                    $totalEventos = isset($eventosProximos) ? count($eventosProximos) : 0;
                    $totalNotificaciones = $totalTareas + $totalEventos + $totalNotifBD;
                ?>
                <a href="#" class="text-secondary position-relative me-2" id="notifDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if ($totalNotificaciones > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            <?= $totalNotificaciones ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="width: 320px; max-height: 450px; overflow-y: auto;">
                    
                    <!-- 1. NOTIFICACIONES DE LA BITÁCORA / SISTEMA -->
                    <li><h6 class="dropdown-header text-uppercase small fw-bold text-primary border-bottom py-2"><i class="bi bi-bell-fill me-1"></i> Notificaciones</h6></li>
                    <?php if (empty($misNotificaciones)): ?>
                        <li class="text-center py-2 text-muted small">Sin notificaciones pendientes.</li>
                    <?php else: ?>
                        <?php foreach ($misNotificaciones as $notif): ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-start p-2 border-bottom bg-light" href="<?= base_url('operaciones/leer-notificacion/' . $notif['id']) ?>">
                                    <div class="me-2">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-exclamation-circle text-primary" style="font-size: 0.85rem;"></i>
                                        </div>
                                    </div>
                                    <div class="text-wrap w-100">
                                        <div class="fw-bold small text-dark" style="font-size: 0.8rem;"><?= esc($notif['titulo']) ?></div>
                                        <div class="text-secondary small mb-1" style="font-size: 0.73rem;"><?= esc($notif['mensaje']) ?></div>
                                        <div class="text-muted text-end" style="font-size: 0.65rem;">
                                            <?= date('d M, h:i a', strtotime($notif['created_at'])) ?>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- 2. PRÓXIMOS EVENTOS -->
                    <li><h6 class="dropdown-header text-uppercase small fw-bold text-muted border-bottom py-2 mt-2"><i class="bi bi-calendar-star me-1"></i> Próximos Eventos</h6></li>
                    <?php if (empty($eventosProximos)): ?>
                        <li class="text-center py-2 text-muted small">Sin eventos próximos.</li>
                    <?php else: ?>
                        <?php foreach ($eventosProximos as $ev): ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-start p-3 border-bottom bg-light" href="<?= base_url('eventos/show/' . $ev['id']) ?>">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-center" style="width: 35px; height: 35px;">
                                            <i class="bi bi-calendar-event text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="text-wrap w-100">
                                        <div class="fw-bold small text-dark"><?= esc($ev['titulo']) ?></div>
                                        <div class="text-secondary small mb-1" style="font-size: 0.75rem;">Para: <?= esc($ev['roles_asistencia'] ?: 'No definidos') ?></div>
                                        <div class="text-muted d-flex justify-content-between w-100" style="font-size: 0.65rem;">
                                            <span><i class="bi bi-clock me-1"></i><?= date('d M', strtotime($ev['fecha_evento'])) ?></span>
                                            <span class="text-primary fw-bold">Ver <i class="bi bi-chevron-right"></i></span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- 3. TAREAS PENDIENTES -->
                    <?php if ($userRole !== 'events_rep' && $userEmail !== 'carlos.sarmiento@forhumancapital.mx'): ?>
                        <li><h6 class="dropdown-header text-uppercase small fw-bold text-muted border-bottom py-2 mt-2"><i class="bi bi-list-check me-1"></i> Tareas Pendientes</h6></li>
                        <?php if (empty($tareasPendientes)): ?>
                            <li class="text-center py-4 text-muted">
                                <i class="bi bi-check2-circle fs-3 d-block mb-2 text-success"></i>
                                <span class="small">¡Estás al día! Sin pendientes.</span>
                            </li>
                        <?php else: ?>
                            <?php foreach ($tareasPendientes as $notif): 
                                $esVencida = strtotime($notif['due_date']) < time();
                            ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-start p-3 border-bottom <?= $esVencida ? 'notif-vencida' : '' ?>" href="<?= base_url('prospectos/perfil/' . $notif['prospect_id']) ?>">
                                        <div class="me-3">
                                            <div class="bg-<?= $esVencida ? 'danger' : 'warning' ?> bg-opacity-10 p-2 rounded-circle text-center" style="width: 35px; height: 35px;">
                                                <i class="bi bi-alarm <?= $esVencida ? 'text-danger' : 'text-warning' ?>"></i>
                                            </div>
                                        </div>
                                        <div class="text-wrap">
                                            <div class="fw-bold small text-dark"><?= esc($notif['company_name']) ?></div>
                                            <div class="text-secondary small mb-1" style="font-size: 0.75rem;"><?= esc($notif['subject']) ?></div>
                                            <div class="text-muted" style="font-size: 0.65rem;">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <span class="<?= $esVencida ? 'text-danger fw-bold' : '' ?>">
                                                    <?= date('d M, h:i a', strtotime($notif['due_date'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-center small text-primary fw-bold py-2 mt-2" href="<?= base_url('dashboard') ?>">Ver Dashboard</a></li>
                    <?php endif; ?>

                </ul>
            </div>

            <!-- PERFIL DE USUARIO Y PERFIL -->
            <div class="dropdown">
                <div class="user-profile" data-bs-toggle="dropdown">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold small"><?= session()->get('name') ?></div>
                        <div class="text-muted small" style="font-size: 0.7rem;"><?= ucfirst(session()->get('role') ?? 'Vendedor') ?></div>
                    </div>
                    <div class="user-avatar shadow-sm">
                        <?= substr(session()->get('name') ?? 'U', 0, 1) ?>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item py-2" href="<?= base_url('perfil') ?>"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger py-2" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="p-4">
        <?= $this->renderSection('contenido') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>