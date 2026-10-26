<?php

namespace App\Controllers;

class DirectorioFormularios extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $userEmail = session()->get('email');
        $userRole  = session()->get('role');

        // Seguridad: Solo Victoria o usuarios con rol 'admin' pueden ingresar
        $esVictoria = ($userEmail === 'admin@forhuman.com' || $userEmail === 'admin@victoria.com');
        if (!$esVictoria && $userRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'No tienes permiso para acceder a este módulo.');
        }

        // Catálogo extraído del archivo Excel
        $formularios = [
            [
                'nombre'      => 'Cotizar Servicios Soluciones Capital Humano Full Skills',
                'categoria'   => 'Cotización',
                'formulario'  => 'https://forhumancapital.mx/formulario-cotizacion/',
                'panel'       => null,
                'password'    => null,
            ],
            [
                'nombre'      => 'Alta Cliente Full Skills',
                'categoria'   => 'Operación',
                'formulario'  => 'https://forhumancapital.mx/alta_cliente/',
                'panel'       => 'https://forhumancapital.mx/alta_cliente/panel.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Alineación Estratégica para Proyectos Full Skills',
                'categoria'   => 'Estrategia',
                'formulario'  => 'https://forhumancapital.mx/alineacion_estrategica/',
                'panel'       => 'https://forhumancapital.mx/alineacion_estrategica/panel.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Proceso Documental Proveedor',
                'categoria'   => 'Proveedores',
                'formulario'  => 'https://forhumancapital.mx/checkproveedor/',
                'panel'       => 'https://forhumancapital.mx/checkproveedor/listado_proveedores.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Encuesta Post Evento Alineación Interna FOR',
                'categoria'   => 'Encuestas',
                'formulario'  => 'https://forhumancapital.mx/encuesta_satisfaccion',
                'panel'       => 'https://forhumancapital.mx/encuesta_satisfaccion/panel.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Evaluación de Competencias (Pruebas para Candidatos)',
                'categoria'   => 'Evaluación',
                'formulario'  => 'https://forhumancapital.mx/evaluacion/',
                'panel'       => 'https://forhumancapital.mx/evaluacion/panel.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Expediente Maestro',
                'categoria'   => 'Expediente',
                'formulario'  => 'https://forhumancapital.mx/expediente_maestro/',
                'panel'       => 'https://forhumancapital.mx/expediente_maestro/',
                'password'    => null,
            ],
            [
                'nombre'      => 'Datos Generales Diagnóstico 360',
                'categoria'   => 'Diagnóstico',
                'formulario'  => 'https://forhumancapital.mx/form_datos_generales/',
                'panel'       => 'https://forhumancapital.mx/form_datos_generales/resultados.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Levantamiento de Casos Reales',
                'categoria'   => 'Diagnóstico',
                'formulario'  => 'https://forhumancapital.mx/formulario/',
                'panel'       => 'https://forhumancapital.mx/formulario/login.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Formulario Alta Asociados',
                'categoria'   => 'Asociados',
                'formulario'  => 'https://forhumancapital.mx/formulario-asociados/',
                'panel'       => 'https://forhumancapital.mx/formulario-asociados/panel.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Cotización Acre',
                'categoria'   => 'Cotización',
                'formulario'  => 'https://forhumancapital.mx/formulario-cotizacion-acre/',
                'panel'       => null,
                'password'    => null,
            ],
            [
                'nombre'      => 'Criterios de Búsqueda de Talento Estratégico Full Skills',
                'categoria'   => 'Atracción de Talento',
                'formulario'  => 'https://forhumancapital.mx/formulario_cbt/',
                'panel'       => 'https://forhumancapital.mx/formulario_cbt/dashboard.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Matriz de Transferencia de Procesos (Semana 3 Onboarding)',
                'categoria'   => 'Onboarding',
                'formulario'  => 'https://forhumancapital.mx/matriz_transferenica_procesos/',
                'panel'       => 'https://forhumancapital.mx/matriz_transferenica_procesos/listado.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Valoración Estratégica de Candidatos Full Skills',
                'categoria'   => 'Atracción de Talento',
                'formulario'  => 'https://forhumancapital.mx/valoracion_candidatos/',
                'panel'       => 'https://forhumancapital.mx/valoracion_candidatos/panel.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Cotizador (Propuestas OPT)',
                'categoria'   => 'Cotización',
                'formulario'  => 'https://forhumancapital.mx/cotizador/',
                'panel'       => null,
                'password'    => 'FOR2026',
            ],
            [
                'nombre'      => 'Calculador',
                'categoria'   => 'Herramientas',
                'formulario'  => 'https://forhumancapital.mx/webapp/calculadora',
                'panel'       => null,
                'password'    => null,
            ],
            [
                'nombre'      => 'Diagnóstico Soluciones',
                'categoria'   => 'Diagnóstico',
                'formulario'  => 'https://forhumancapital.mx/formulario_soluciones',
                'panel'       => 'https://forhumancapital.mx/formulario_soluciones/resultados.php',
                'password'    => null,
            ],
            [
                'nombre'      => 'Diagnóstico Optimización',
                'categoria'   => 'Diagnóstico',
                'formulario'  => 'https://forhumancapital.mx/formulario_optimizacion',
                'panel'       => 'https://forhumancapital.mx/formulario_optimizacion/resultados.php',
                'password'    => null,
            ]
        ];

        $data = [
            'titulo'      => 'Directorio de Formularios y Paneles',
            'formularios' => $formularios
        ];

        return view('directorio_formularios/index', $data);
    }
}