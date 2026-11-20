<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/** The same catalog drives the role editor, navigation and server authorization. */
class Access extends BaseConfig
{
    public array $modules = [
        'dashboard' => ['Inicio', 'dashboard', 'bi-grid-1x2-fill', 'General'],
        'pipeline' => ['Pipeline Ventas', 'dashboard/pipeline', 'bi-kanban', 'Operación'],
        'carga' => ['Carga Semanal', 'carga-comercial', 'bi-calendar2-week', 'Operación'],
        'eventos' => ['Eventos', 'eventos', 'bi-calendar-event', 'Operación'],
        'prospectos' => ['Perfilamiento', 'prospectos', 'bi-list-task', 'Operación'],
        'operaciones' => ['Bitácora Operativa', 'operaciones', 'bi-life-preserver', 'Operación'],
        'forecast' => ['Forecast', 'forecast', 'bi-bullseye', 'Operación'],
        'vacantes' => ['Pipeline Vacantes', 'vacantes', 'bi-kanban', 'Atracción de Talento'],
        'clientes' => ['Clientes', 'clientes', 'bi-building', 'Administración'],
        'socios' => ['Asociados FOR', 'socios', 'bi-person-badge', 'Administración'],
        'cierre_socios' => ['Cierre de Mes (Asociados)', 'socios/cierre-mes', 'bi-calendar-check', 'Administración'],
        'ventas' => ['Ventas e Ingresos', 'ventas', 'bi-cash-coin', 'Administración'],
        'importar' => ['Cierre Mensual', 'importar', 'bi-cloud-upload', 'Administración'],
        'directorio' => ['Directorio Formularios', 'directorio-formularios', 'bi-journal-code', 'Herramientas Web'],
        'salarios' => ['Guía Global de Salarios 2027', 'salarios', 'bi-journal-text', 'Herramientas Web'],
        'alineacion' => ['Alineación Estratégica', 'formularios/alineacion', 'bi-file-earmark-check', 'Estrategia'],
        'calculadora' => ['Calculadora Nómina', 'calculadora', 'bi-calculator', 'Herramientas'],
        'cotizador' => ['Cotizador', 'cotizador', 'bi-calculator', 'Herramientas'],
    ];

    public array $scopes = [
        'prospectos.ver_todos' => 'Perfilamiento: consultar todos en el listado',
        'prospectos.ver_optimizacion' => 'Perfilamiento: consultar Optimización en el listado',
        'carga.todos_canales' => 'Carga semanal: consultar y capturar en todos los canales',
        'operaciones.ver_todos' => 'Operaciones: consultar todos los tickets en el listado',
        'operaciones.firmar' => 'Operaciones: firmar como supervisor',
    ];

    /** Each public controller action must be classified explicitly. */
    public array $actions = [
        'Dashboard' => ['index' => 'dashboard.ver', 'pipeline' => 'pipeline.ver', 'updateStage' => 'pipeline.modificar'],
        'MatrizFullSkills' => ['index' => 'matriz_full_skills.ver'],
        'Prospectos' => [
            'index' => 'prospectos.ver', 'perfil' => 'prospectos.ver', 'exportarPDF' => 'prospectos.ver',
            'nuevo' => 'prospectos.modificar', 'guardar' => 'prospectos.modificar', 'editar' => 'prospectos.modificar',
            'actualizar' => 'prospectos.modificar', 'guardarActividad' => 'prospectos.modificar',
            'completarActividad' => 'prospectos.modificar', 'subirDocumento' => 'prospectos.modificar',
            'eliminarDocumento' => 'prospectos.modificar',
        ],
        'CargaComercial' => ['index' => 'carga.ver', 'guardar' => 'carga.modificar'],
        'Eventos' => ['index' => 'eventos.ver', 'show' => 'eventos.ver', 'guardar' => 'eventos.modificar', 'editar' => 'eventos.modificar', 'actualizar' => 'eventos.modificar'],
        'Vacantes' => ['index' => 'vacantes.ver', 'crear' => 'vacantes.modificar', 'guardar' => 'vacantes.modificar', 'actualizar' => 'vacantes.modificar'],
        'Clientes' => ['index' => 'clientes.ver', 'show' => 'clientes.ver', 'editar' => 'clientes.modificar', 'actualizar' => 'clientes.modificar'],
        'Socios' => ['index' => 'socios.ver', 'show' => 'socios.ver', 'editar' => 'socios.modificar', 'actualizar' => 'socios.modificar', 'guardar' => 'socios.modificar', 'cierreMes' => 'cierre_socios.ver', 'guardarSeguimiento' => 'cierre_socios.modificar', 'actualizarSeguimiento' => 'cierre_socios.modificar'],
        'Ventas' => ['index' => 'ventas.ver', 'editar' => 'ventas.modificar', 'guardar' => 'ventas.modificar', 'actualizar' => 'ventas.modificar'],
        'Importar' => ['index' => 'importar.ver', 'ventas' => 'importar.ver', 'procesar' => 'importar.modificar', 'procesarVentasCsv' => 'importar.modificar', 'procesarUnificado' => 'importar.modificar'],
        'CortesSemanal' => ['index' => 'carga.ver', 'guardar' => 'carga.modificar'],
        'Forecast' => ['index' => 'forecast.ver', 'getYearData' => 'forecast.ver', 'save' => 'forecast.modificar'],
        'Operaciones' => ['index' => 'operaciones.ver', 'atender' => 'operaciones.ver', 'leerNotificacion' => 'operaciones.ver', 'nuevo' => 'operaciones.modificar', 'guardar' => 'operaciones.modificar', 'actualizar' => 'operaciones.modificar'],
        'DirectorioFormularios' => ['index' => 'directorio.ver'],
        'Salarios' => ['index' => 'salarios.ver', 'puestos' => 'salarios.ver', 'perfil' => 'salarios.ver', 'exportar' => 'salarios.ver'],
        'Formularios' => ['alineacion' => 'alineacion.ver', 'descargarPdf' => 'alineacion.ver', 'guardarAlineacion' => 'alineacion.modificar'],
        'Calculadora' => ['index' => 'calculadora.ver'],
        'Cotizador' => ['index' => 'cotizador.ver', 'ver' => 'cotizador.ver', 'nuevo' => 'cotizador.modificar', 'editar' => 'cotizador.modificar', 'guardar' => 'cotizador.modificar', 'actualizar' => 'cotizador.modificar'],
    ];

    public function permissions(): array
    {
        $permissions = [];
        foreach ($this->modules as $key => $module) {
            $permissions[$key . '.ver'] = $module[0] . ': consultar';
            $permissions[$key . '.modificar'] = $module[0] . ': modificar';
        }
        // This private permission is resolved by account ID, not by a role grant.
        return $permissions + $this->scopes + ['matriz_full_skills.ver' => 'Matriz Full Skills: acceso privado'];
    }
}
