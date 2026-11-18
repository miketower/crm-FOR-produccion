<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');       // La raíz ahora es el Login
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Rutas para el Módulo CRM
$routes->get('prospectos/nuevo', 'Prospectos::nuevo'); // Mostrar formulario
$routes->post('prospectos/guardar', 'Prospectos::guardar'); // Recibir datos
$routes->get('prospectos', 'Prospectos::index'); // URL: localhost:8080/prospectos
$routes->get('importar', 'Importar::index');
$routes->post('importar/procesar', 'Importar::procesar');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('matriz-full-skills', 'MatrizFullSkills::index');
$routes->get('prospectos/editar/(:num)', 'Prospectos::editar/$1');
$routes->post('prospectos/actualizar/(:num)', 'Prospectos::actualizar/$1');
$routes->get('dashboard/pipeline', 'Dashboard::pipeline');
$routes->post('dashboard/updateStage', 'Dashboard::updateStage');
// RUTAS DE USUARIOS
$routes->get('usuarios', 'Usuarios::index');
$routes->get('usuarios/nuevo', 'Usuarios::nuevo');
$routes->post('usuarios/guardar', 'Usuarios::guardar', ['filter' => 'csrf']);
$routes->post('usuarios/eliminar/(:num)', 'Usuarios::eliminar/$1', ['filter' => 'csrf']);
$routes->get('usuarios/accesos', 'Accesos::index');
$routes->post('usuarios/accesos/usuario/(:num)', 'Accesos::guardarUsuario/$1', ['filter' => 'csrf']);
$routes->post('usuarios/accesos/roles', 'Accesos::guardarRol', ['filter' => 'csrf']);
$routes->post('usuarios/accesos/roles/(:num)', 'Accesos::guardarRol/$1', ['filter' => 'csrf']);
$routes->get('sin-acceso', 'Auth::sinAcceso');
// PERFIL DE USUARIO
$routes->get('perfil', 'Perfil::index');
$routes->post('perfil/actualizar', 'Perfil::actualizar');
//Notificaciones
$routes->get('api/notificaciones', 'Notificaciones::obtenerUltimas');
$routes->post('api/notificaciones/leer', 'Notificaciones::marcarLeidas');
$routes->get('notificaciones', 'Notificaciones::index');
// Módulo de Seguimiento (Bitácora)
$routes->get('/prospectos/perfil/(:num)', 'Prospectos::perfil/$1');
$routes->post('/prospectos/actividad/guardar', 'Prospectos::guardarActividad');
$routes->get('/prospectos/actividad/completar/(:num)', 'Prospectos::completarActividad/$1');
$routes->post('/prospectos/documento/subir', 'Prospectos::subirDocumento');
$routes->get('prospectos/exportar/(:num)', 'Prospectos::exportarPDF/$1');
$routes->get('prospectos/documento/eliminar/(:num)', 'Prospectos::eliminarDocumento/$1');
$routes->match(['get', 'post'], 'calculadora', 'Calculadora::index');
// Formularios
$routes->get('formularios/alineacion', 'Formularios::alineacion');
$routes->post('formularios/guardarAlineacion', 'Formularios::guardarAlineacion');
$routes->get('formularios/descargarPdf/(:num)', 'Formularios::descargarPdf/$1');
// ==========================================
// 💼 RUTAS DEL MÓDULO DE CLIENTES COMERCIALES
// ==========================================
$routes->get('clientes', 'Clientes::index');
$routes->get('clientes/editar/(:num)', 'Clientes::editar/$1');
$routes->post('clientes/actualizar/(:num)', 'Clientes::actualizar/$1');
// Dejamos esta lista por si en el futuro habilitas el detalle visual
$routes->get('clientes/detalle/(:num)', 'Clientes::show/$1');
// Módulo de Socios
$routes->get('socios', 'Socios::index');
$routes->get('socios/show/(:num)', 'Socios::show/$1');
$routes->get('socios/editar/(:num)', 'Socios::editar/$1');
$routes->post('socios/actualizar/(:num)', 'Socios::actualizar/$1');
$routes->post('socios/guardar', 'Socios::guardar');

// Rutas de Cierre de Mes dentro de Socios
$routes->get('socios/cierre-mes', 'Socios::cierreMes');
$routes->post('socios/guardar-seguimiento', 'Socios::guardarSeguimiento');
// Módulo de Ventas / Flujos
$routes->get('ventas', 'Ventas::index');
$routes->post('ventas/guardar', 'Ventas::guardar');
$routes->get('ventas/editar/(:num)', 'Ventas::editar/$1');
$routes->post('ventas/actualizar/(:num)', 'Ventas::actualizar/$1');
$routes->get('importar/ventas', 'Importar::ventas');
$routes->post('importar/procesarVentasCsv', 'Importar::procesarVentasCsv');
$routes->post('importar/procesarUnificado', 'Importar::procesarUnificado');
//Corte semanal
$routes->get('cortes-semanales', 'CortesSemanal::index');
$routes->post('cortes-semanales/guardar', 'CortesSemanal::guardar');
//Forecast
$routes->get('forecast', 'Forecast::index');
$routes->get('forecast/getYearData/(:num)', 'Forecast::getYearData/$1');
$routes->post('forecast/save', 'Forecast::save');
//Eventos
$routes->group('eventos', function($routes) {
    $routes->get('/', 'Eventos::index');
    $routes->post('guardar', 'Eventos::guardar');
    $routes->get('show/(:num)', 'Eventos::show/$1');
    
    // --- NUEVAS RUTAS PARA EDICIÓN ---
    $routes->get('editar/(:num)', 'Eventos::editar/$1');
    $routes->post('actualizar/(:num)', 'Eventos::actualizar/$1');
});

$routes->get('carga-comercial', 'CargaComercial::index');
$routes->post('carga-comercial/guardar', 'CargaComercial::guardar');

$routes->group('vacantes', function($routes) {
    $routes->get('/', 'Vacantes::index');
    $routes->get('tv', 'Vacantes::tvPipeline');
    $routes->get('crear', 'Vacantes::crear');
    $routes->post('guardar', 'Vacantes::guardar');
    $routes->post('actualizar', 'Vacantes::actualizar');
    $routes->get('getVacantesJson', 'Vacantes::getVacantesJson');
});

$routes->get('cotizador', 'Cotizador::index');           // Muestra la tabla de arriba
$routes->get('cotizador/nuevo', 'Cotizador::nuevo');     // Muestra el formulario
$routes->post('cotizador/guardar', 'Cotizador::guardar'); // Procesa el AJAX
$routes->get('cotizador/ver/(:num)', 'Cotizador::ver/$1');
$routes->get('cotizador/editar/(:num)', 'Cotizador::editar/$1');
// Preparando la ruta para cuando guardes la edición:
$routes->post('cotizador/actualizar/(:num)', 'Cotizador::actualizar/$1');

// =======================================================
// MÓDULO DE OPERACIONES (Mesa de Ayuda)
// =======================================================
$routes->group('operaciones', function($routes) { 
    $routes->get('/', 'Operaciones::index');
    $routes->get('nuevo', 'Operaciones::nuevo');
    $routes->post('guardar', 'Operaciones::guardar');
    $routes->get('atender/(:num)', 'Operaciones::atender/$1');
    $routes->post('actualizar/(:num)', 'Operaciones::actualizar/$1');
    
    // AQUÍ ESTÁ EL CAMBIO: No lleva la palabra "operaciones/" al inicio porque ya está dentro del group()
    $routes->get('leer-notificacion/(:num)', 'Operaciones::leerNotificacion/$1');
});

$routes->get('directorio-formularios', 'DirectorioFormularios::index');

$routes->group('salarios', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('/', 'Salarios::index');
    $routes->get('puestos', 'Salarios::puestos');
    $routes->get('exportar', 'Salarios::exportar');
    $routes->get('perfil/(:num)', 'Salarios::perfil/$1');
});
