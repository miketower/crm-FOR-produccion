<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Clientes extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!session()->get('isLoggedIn')) {
            header('Location: '.base_url('/'));
            exit; 
        }
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();
        $pager = \Config\Services::pager();

        // 1. Captura de filtros (Inician vacíos para mostrar TODO)
        $fechaInicio = $request->getGet('fecha_inicio') ?? '';
        $fechaFin    = $request->getGet('fecha_fin') ?? '';
        $search      = $request->getGet('search') ?? '';

        // 2. Consulta base apuntando a la tabla unificada 'prospects'
        $builder = $db->table('prospects');
        $builder->select('
            prospects.id,
            prospects.company_name,
            prospects.rfc,
            prospects.nombre_contacto,
            prospects.telefono_contacto,
            prospects.correo_electronico,
            prospects.ticket_value,
            prospects.last_stage_change_date,
            service_types.name as service_name,
            states.name as state_name
        ');
        $builder->join('service_types', 'service_types.id = prospects.service_type_id', 'left');
        $builder->join('states', 'states.id = prospects.state_id', 'left');
        
        // CONDICIÓN PRINCIPAL: Solo ganados (Clientes reales)
        $builder->where('prospects.pipeline_stage', 'Cerrado Ganado');
        $builder->where('prospects.deleted_at', null);
        
        // FILTRO DINÁMICO DE FECHAS: Solo si el usuario seleccionó un rango
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $builder->where("prospects.last_stage_change_date BETWEEN '{$fechaInicio}' AND '{$fechaFin}'");
        }

        // BÚSQUEDA POR PALABRA CLAVE
        if (!empty($search)) {
            $builder->groupStart()
                        ->like('prospects.company_name', $search)
                        ->orLike('prospects.rfc', $search)
                    ->groupEnd();
        }

        // Orden descendente por defecto: Los últimos cerrados aparecen hasta arriba de la lista
        $builder->orderBy('prospects.last_stage_change_date', 'DESC');
        $builder->orderBy('prospects.id', 'DESC');

        // 3. Paginación
        $perPage = 15;
        $total = $builder->countAllResults(false);
        $page = (int)($request->getGet('page') ?? 1);
        $clientes = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $data = [
            'clientes'     => $clientes,
            'pager'        => $pager->makeLinks($page, $perPage, $total, 'bootstrap_full'),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
            'search'       => $search,
            'titulo'       => 'Cartera Global de Clientes'
        ];

        return view('clientes/index', $data);
    }

    public function editar($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Buscamos el registro del cliente
        $cliente = $db->table('prospects')->where('id', $id)->get()->getRowArray();
        
        if (!$cliente) {
            return redirect()->to('/clientes')->with('msg', 'Cliente no encontrado.');
        }

        // 2. Traemos el catálogo de socios completo para las listas desplegables
        $socios = $db->table('socios')
                    ->select('id, nombre')
                    ->orderBy('nombre', 'ASC')
                    ->get()
                    ->getResultArray();

        $data = [
            'cliente' => $cliente,
            'socios'  => $socios, // <--- Enviamos los socios a la vista
            'titulo'  => 'Editar Expediente Comercial'
        ];

        return view('clientes/editar', $data);
    }

    public function actualizar($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Obtener el registro actual
        $clienteActual = $db->table('prospects')->where('id', $id)->get()->getRowArray();
        
        if (!$clienteActual) {
            return redirect()->to('/clientes')->with('msg', 'Error: Cliente no encontrado.');
        }

        // 2. Procesar el bloqueo de seguridad corporativo
        $yaValidado = ($clienteActual['informacion_validada'] == 1);
        
        // Solo el usuario con ID = 1 puede cambiar el estado del switch
        if (session()->get('id') == 1) {
            $nuevoEstatusValidado = $this->request->getPost('informacion_validada') ? 1 : 0;
            // Si el usuario 1 acaba de apagar el switch en esta petición, liberamos el bloqueo para este guardado
            if ($nuevoEstatusValidado == 0) { $yaValidado = false; }
        } else {
            $nuevoEstatusValidado = $clienteActual['informacion_validada'];
        }

        $serviciosArray = $this->request->getPost('servicios_contratados') ?? [];
        $serviciosString = implode(',', $serviciosArray);

        // 3. CAPTURA CONDICIONAL DE COMISIONES (Protección contra inputs disabled)
        if ($yaValidado) {
            // Si estaba bloqueado, mantenemos exactamente lo que ya estaba en la BD
            $comisionista_id      = $clienteActual['comisionista_id'];
            $red_socio_id         = $clienteActual['red_socio_id'];
            $comisionista_seg_id  = $clienteActual['comisionista_seguimiento_id'];
            $finalPctComisionista = $clienteActual['pct_comisionista_asignado'];
            $finalPctRed          = $clienteActual['pct_red_asignado'];
            $finalPctSeguimiento  = $clienteActual['pct_seguimiento_asignado'];
        } else {
            // Si NO está bloqueado, leemos los inputs del formulario normalmente
            $comisionista_id = $this->request->getPost('comisionista_id');
            $red_socio_id    = $this->request->getPost('red_socio_id');
            $comisionista_seg_id = $this->request->getPost('comisionista_seguimiento_id') ?: null;

            $pctComisionistaManual = $this->request->getPost('pct_comisionista_asignado');
            $pctRedManual          = $this->request->getPost('pct_red_asignado');
            $pctSeguimientoManual  = $this->request->getPost('pct_seguimiento_asignado');

            // Corremos el motor automático
            $porcentajesAuto = $this->obtenerPorcentajesDeComision($clienteActual['service_type_id'], $comisionista_id, $red_socio_id);

            // Prioridad manual sobre automático
            $finalPctComisionista = ($pctComisionistaManual !== '' && $pctComisionistaManual !== null) ? (float)$pctComisionistaManual : $porcentajesAuto['comisionista'];
            $finalPctRed          = ($pctRedManual !== '' && $pctRedManual !== null) ? (float)$pctRedManual : $porcentajesAuto['red'];
            $finalPctSeguimiento  = ($pctSeguimientoManual !== '' && $pctSeguimientoManual !== null) ? (float)$pctSeguimientoManual : $porcentajesAuto['seguimiento'];
        }

        // 4. Armamos el set de actualización completo
        $dataUpdate = [
            // Sección 1
            'origen_prospecto'           => $this->request->getPost('origen_prospecto'),
            'fecha_alta_formato'         => $this->request->getPost('fecha_alta_formato') ?: null,
            'tipo_persona'               => $this->request->getPost('tipo_persona'),
            'company_name'               => $this->request->getPost('company_name'),
            'rfc'                        => strtoupper(trim($this->request->getPost('rfc'))),
            'actividad_principal'        => $this->request->getPost('actividad_principal'),
            'domicilio_fiscal'           => $this->request->getPost('domicilio_fiscal'),
            'nombre_contacto'            => $this->request->getPost('nombre_contacto'),
            'telefono_contacto'          => $this->request->getPost('telefono_contacto'),
            'correo_electronico'         => $this->request->getPost('correo_electronico'),
            'domicilio_envio_docs'       => $this->request->getPost('domicilio_envio_docs'),
            'nombre_recibe_docs'         => $this->request->getPost('nombre_recibe_docs'),
            'servicios_contratados'      => $serviciosString,

            // Sección 2
            'esquema_servicio_proem'     => $this->request->getPost('esquema_servicio_proem'),
            'fecha_inicio_operacion'     => $this->request->getPost('fecha_inicio_operacion') ?: null,
            'condiciones_iva'            => $this->request->getPost('condiciones_iva'),
            'monto_tentativo_operacion'  => $this->request->getPost('monto_tentativo_operacion') ?: 0.00,
            'comision_base_cliente'      => $this->request->getPost('comision_base_cliente'),
            'personas_remunera'          => $this->request->getPost('personas_remunera'),
            'profesion'                  => $this->request->getPost('profesion'),
            'ingresos_otras_actividades' => $this->request->getPost('ingresos_otras_actividades'),
            'cuales_otras_actividades'   => $this->request->getPost('cuales_otras_actividades'),
            'banco'                      => $this->request->getPost('banco'),
            'clabe_interbancaria'        => $this->request->getPost('clabe_interbancaria'),

            // Sección 3: Valores Protegidos o Calculados
            'comisionista_id'             => $comisionista_id ?: null,
            'red_socio_id'                => $red_socio_id ?: null,
            'comisionista_seguimiento_id' => $comisionista_seg_id,
            
            'pct_comisionista_asignado'   => $finalPctComisionista,
            'pct_red_asignado'            => $finalPctRed,
            'pct_seguimiento_asignado'    => $finalPctSeguimiento,
            
            // Bandera de Autorización
            'informacion_validada'        => $nuevoEstatusValidado,
            
            'tipo_entidad'                => 'Cliente', 
            'updated_at'                  => date('Y-m-d H:i:s')
        ];

        $db->table('prospects')->where('id', $id)->update($dataUpdate);

        return redirect()->to('/clientes')->with('msg', '¡Expediente actualizado! Control de seguridad verificado.');
    }
    // =========================================================
    // 🧠 MOTOR DE CÁLCULO AUTOMÁTICO (AQUÍ VA LA FUNCIÓN)
    // =========================================================
    private function obtenerPorcentajesDeComision($service_type_id, $comisionista_id, $red_socio_id)
    {
        $db = \Config\Database::connect();
        
        $pctComisionista = 0.00;
        $pctRed = 0.00;
        $pctSeguimiento = 0.00;

        if (empty($comisionista_id) || empty($service_type_id)) {
            return ['comisionista' => $pctComisionista, 'red' => $pctRed, 'seguimiento' => $pctSeguimiento];
        }

        // 1. Validamos el tipo de socio del comisionista (Patrimonial mantiene su regla de oro)
        $socio = $db->table('socios s')
            ->select('s.porcentaje_personalizado, t.nombre as tipo_nombre')
            ->join('cat_tipos_socios t', 't.id = s.cat_tipo_socio_id', 'left')
            ->where('s.id', $comisionista_id)
            ->get()->getRow();

        if ($socio && $socio->tipo_nombre == 'Patrimonial') {
            // Caso Socio Patrimonial: 42% fijo (o su personalizado) y nunca lleva red
            $pctComisionista = (!empty($socio->porcentaje_personalizado)) ? (float)$socio->porcentaje_personalizado : 42.00;
            return ['comisionista' => $pctComisionista, 'red' => 0.00, 'seguimiento' => 0.00];
        }

        // 2. Caso Estándar / Prosper: Consultamos directamente los campos fijos del catálogo service_types
        $servicio = $db->table('service_types')->where('id', $service_type_id)->get()->getRow();
        
        if ($servicio) {
            // Si el comisionista tiene un trato VIP personalizado en su ficha, lo respetamos; si no, el del servicio
            $pctComisionista = (!empty($socio->porcentaje_personalizado)) ? (float)$socio->porcentaje_personalizado : (float)$servicio->pct_comisionista_base;
            
            // La red solo se calcula si seleccionaron una Red en el formulario
            $pctRed = !empty($red_socio_id) ? (float)$servicio->pct_red_base : 0.00;
            
            $pctSeguimiento = (float)$servicio->pct_seguimiento_base;
        }

        return [
            'comisionista' => $pctComisionista,
            'red'          => $pctRed,
            'seguimiento'  => $pctSeguimiento
        ];
    }
}