<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ActivityModel;
use App\Models\DocumentModel;

class Prospectos extends BaseController
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

        // 1. Lógica de Fechas
        $fechaInicio = $request->getGet('fecha_inicio') ?? date('Y-m-01');
        $fechaFin = $request->getGet('fecha_fin') ?? date('Y-m-t');

        // 2. Inteligencia de grupos (Macros y Estados)
        $sqlMacro = "SELECT m.id, COUNT(p.id) as total, SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
                    FROM categorias_macro m 
                    JOIN giros g ON g.categoria_id = m.id 
                    JOIN prospects p ON p.industry_id = g.id
                    WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión') 
                    GROUP BY m.id";
        
        $rawMacro = $db->query($sqlMacro)->getResultArray();
        $pesosMacro = [];
        foreach($rawMacro as $r) {
            if($r['total'] > 0) $pesosMacro[$r['id']] = ($r['ganados'] / $r['total']) * 100;
        }

        $sqlEstado = "SELECT e.id, COUNT(p.id) as total, SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
                    FROM states e 
                    JOIN prospects p ON p.state_id = e.id
                    WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión') 
                    GROUP BY e.id";
                    
        $rawEstado = $db->query($sqlEstado)->getResultArray();
        $pesosEstado = [];
        foreach($rawEstado as $r) {
            if($r['total'] > 0) $pesosEstado[$r['id']] = ($r['ganados'] / $r['total']) * 100;
        }

        // 3. Consulta principal
        $builder = $db->table('prospects');
        $builder->select('
            prospects.*, 
            (SELECT SUM(ingreso_bruto_total) FROM ventas_flujos WHERE prospect_id = prospects.id AND fecha_vendido BETWEEN "'.$fechaInicio.'" AND "'.$fechaFin.'") as total_ingreso_bruto,
            (SELECT SUM(comision_bruta) FROM ventas_flujos WHERE prospect_id = prospects.id AND fecha_vendido BETWEEN "'.$fechaInicio.'" AND "'.$fechaFin.'") as total_comision,
            categorias_macro.nombre_categoria as nombre_macro,
            service_types.name as service_name,
            states.name as state_name
        ');
        
        $builder->join('categorias_macro', 'categorias_macro.id = prospects.industry_id', 'left');
        $builder->join('service_types', 'service_types.id = prospects.service_type_id', 'left');
        $builder->join('states', 'states.id = prospects.state_id', 'left');
        
        if ($search = $request->getGet('search')) {
            $builder->groupStart()
                    ->like('prospects.company_name', $search)
                    ->orLike('states.name', $search)
                    ->groupEnd();
        }

        // Filtro por Servicio
        if ($servicio = $request->getGet('servicio_filtro')) {
            $builder->where('service_types.name', $servicio);
        }

        // Filtro por Macro / Giro
        if ($macro = $request->getGet('macro_filtro')) {
            // Se usa LOWER porque en la vista mandas el valor en minúsculas
            $builder->where('LOWER(categorias_macro.nombre_categoria)', $macro);
        }

        // Filtro por Etapa
        if ($etapa = $request->getGet('etapa_filtro')) {
            $builder->where('prospects.pipeline_stage', $etapa);
        }

        // Filtro por Grupo (Ya lo tenías, lo mantenemos)
        if ($grupo = $request->getGet('grupo')) {
            $builder->where('prospects.classification_group', $grupo);
        }
        
       // --- INICIO: FILTRO DE SEGURIDAD POR USUARIO Y PERMISOS ESPECÍFICOS ---
       $userEmailSession = session()->get('email');

       if (session()->get('role') !== 'admin') {
           if ($userEmailSession === 'danya.cruz@forhumancapital.mx') {
               // Danya puede ver TODOS los perfilamientos (no aplicamos filtro restrictivo de creador)
           } 
           elseif ($userEmailSession === 'david.zapata@forhumancapital.mx' || session()->get('role') === 'commercial_rep') {
               // David Zapata solo debe ver los perfilamientos que sean de tipo "Optimización"
               $builder->where('service_types.name', 'Optimización');
           } 
           else {
               // Resto de usuarios normales: solo ven lo que ellos crearon
               $builder->where('prospects.created_by', session()->get('id'));
           }
       }
        // --- FIN: FILTRO DE SEGURIDAD POR USUARIO ---
        
        // --- FIN DE NUEVOS FILTROS BACKEND ---

        $builder->orderBy('prospects.created_at', 'DESC');

        // 4. Paginación
        $perPage = 15;
        $total = $builder->countAllResults(false);
        $page = (int)($request->getGet('page') ?? 1);
        $prospectos = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        // 5. Cálculo de Viabilidad
        foreach ($prospectos as &$p) {
            $score = 0;
            $ticket = floatval($p['ticket_value']);
            if ($ticket >= 1500000) $score += 70;
            elseif ($ticket > 0) $score += ($ticket / 1500000) * 70;

            $servicio = strtoupper($p['service_name'] ?? ''); 
            if (strpos($servicio, 'ESPECIALIZADOS') !== false) $score += 15;
            elseif (strpos($servicio, 'PROEM') !== false) $score += 10;
            elseif (!empty($servicio)) $score += 5;

            $macroId = $p['industry_id'];
            if (!empty($macroId) && isset($pesosMacro[$macroId])) {
                $score += ($pesosMacro[$macroId] > 50) ? 5 : 3;
            } elseif (!empty($macroId)) {
                $score += 2; 
            }

            $stateId = $p['state_id'];
            if (!empty($stateId) && isset($pesosEstado[$stateId])) {
                $score += ($pesosEstado[$stateId] > 50) ? 5 : 3;
            } elseif (!empty($stateId)) {
                $score += 2; 
            }

            $comp = intval($p['complexity_level'] ?: 3);
            if ($comp == 1) $score += 5;
            elseif ($comp == 2) $score += 4;
            elseif ($comp == 3) $score += 3;
            elseif ($comp == 4) $score += 2;
            else $score += 1;

            $p['viability_score'] = min(round($score), 100);
        }
        unset($p);

        $catServicios = $db->table('service_types')->orderBy('name', 'ASC')->get()->getResultArray();
        $catIndustrias = $db->table('categorias_macro')->orderBy('nombre_categoria', 'ASC')->get()->getResultArray();

        $data = array_merge($this->viewData, [
            'prospectos'   => $prospectos,
            'pager'        => $pager->makeLinks($page, $perPage, $total, 'bootstrap_full'),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
            'grupos'       => ['A', 'B', 'C', 'D'],
            // Enviamos los catálogos dinámicos a la vista
            'cat_servicios'  => $catServicios,
            'cat_industrias' => $catIndustrias
        ]);

        return view('prospectos/listado', $data);
    }

    // FORMULARIO NUEVO
    public function nuevo()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        $db = \Config\Database::connect();

        // --- A. CONSULTAS PARA LLENAR LOS SELECTS (DROPDOWNS) ---

        // 1. Categorías Macro
        $categorias = $db->table('categorias_macro')
                         ->orderBy('nombre_categoria', 'ASC') 
                         ->get()->getResultArray();

                         // Cargar nuevos catálogos
        $tipos_ventas = $db->table('cat_tipos_ventas')->where('estatus', 1)->orderBy('nombre', 'ASC')->get()->getResultArray();
        $socios = $db->table('socios')->orderBy('nombre', 'ASC')->get()->getResultArray();

        // 2. Tipos de Servicio
        $servicios = $db->table('service_types')
                        ->orderBy('name', 'ASC')
                        ->get()->getResultArray();

        $estados    = $db->table('states')->orderBy('name', 'ASC')->get()->getResultArray();
        
        // --- B. CÁLCULO DE INTELIGENCIA (VIABILIDAD) ---

        // 3. Stats por Macro Categoría
        $sqlMacro = "
            SELECT 
                m.id as macro_id,
                COUNT(p.id) as total_cerrados,
                SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM categorias_macro m
            JOIN giros g ON g.categoria_id = m.id
            JOIN prospects p ON p.industry_id = g.id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            GROUP BY m.id
        ";
        $statsMacro = $db->query($sqlMacro)->getResultArray();

        $pesosMacro = [];
        foreach($statsMacro as $stat) {
            if($stat['total_cerrados'] > 0) {
                $winRate = ($stat['ganados'] / $stat['total_cerrados']) * 100;
                $pesosMacro[$stat['macro_id']] = round($winRate);
            }
        }

        // 4. Stats por Decisor
        $sqlDecisor = "
            SELECT 
                d.decision_maker_role, 
                COUNT(p.id) as total, 
                SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados 
            FROM prospects p
            JOIN decision_profile d ON p.id = d.prospect_id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            AND d.decision_maker_role IS NOT NULL 
            GROUP BY d.decision_maker_role
        ";
        $statsDecisor = $db->query($sqlDecisor)->getResultArray();
        
        $pesosDecisor = [];
        foreach($statsDecisor as $stat) {
            if($stat['total'] > 0) {
                $pesosDecisor[$stat['decision_maker_role']] = round(($stat['ganados'] / $stat['total']) * 100);
            }
        }

        // 5. Stats por Tipo de Servicio (NUEVO) 🧠
        $sqlServicio = "
            SELECT 
                s.id as service_id,
                COUNT(p.id) as total_cerrados,
                SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM service_types s
            JOIN prospects p ON p.service_type_id = s.id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            GROUP BY s.id
        ";
        $statsServicio = $db->query($sqlServicio)->getResultArray();

        $pesosServicio = [];
        foreach($statsServicio as $stat) {
            if($stat['total_cerrados'] > 0) {
                $winRate = ($stat['ganados'] / $stat['total_cerrados']) * 100;
                $pesosServicio[$stat['service_id']] = round($winRate);
            }
        }

        $sqlEstado = "
            SELECT 
                e.id as state_id,
                COUNT(p.id) as total_cerrados,
                SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM states e
            JOIN prospects p ON p.state_id = e.id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            GROUP BY e.id
        ";
        $statsEstado = $db->query($sqlEstado)->getResultArray();

        $pesosEstado = [];
        foreach($statsEstado as $stat) {
            if($stat['total_cerrados'] > 0) {
                $winRate = ($stat['ganados'] / $stat['total_cerrados']) * 100;
                $pesosEstado[$stat['state_id']] = round($winRate);
            }
        }

        $data = array_merge($this->viewData, [
            'categorias'    => $categorias,
            'servicios'     => $servicios,
            'estados'       => $estados,
            'pesosMacro'    => $pesosMacro ?? [],
            'pesosDecisor'  => $pesosDecisor ?? [],
            'pesosServicio' => $pesosServicio ?? [],
            'pesosEstado'   => $pesosEstado ?? [],
            'tipos_ventas' => $tipos_ventas,
            'socios'       => $socios
        ]);

        return view('prospectos/formulario_nuevo', $data);
    }

    // GUARDAR NUEVO
    public function guardar()
    {
        $db = \Config\Database::connect();
        
        // 1. Insertar en tabla PADRE (prospects)
        // 1. A. Actualizar tabla PROSPECTS
        
        // Procesar selección múltiple de asistentes a cita
        $asistentes = $this->request->getPost('asistentes_cita');
        $asistentes_str = !empty($asistentes) ? implode(', ', $asistentes) : null;

        $dataProspect = [
            'company_name'          => $this->request->getPost('company_name'),
            'industry_id'           => $this->request->getPost('industry_id'),      
            'service_type_id'       => $this->request->getPost('service_type_id'),  
            'state_id'              => $this->request->getPost('state_id'),
            'employees_count'       => $this->request->getPost('employees_count') ?: 0,
            'classification_group'  => $this->request->getPost('classification_group'),
            'ticket_value'          => $this->request->getPost('ticket_value') ?: 0.00,
            'complexity_level'      => $this->request->getPost('complexity_level') ?: 3,
            'duration_months'       => $this->request->getPost('duration_months') ?: 0,
            'pipeline_stage'        => 'Nuevo',
            
            // --- ESTAS DOS LÍNEAS DEBEN ESTAR SÍ O SÍ ---
            'created_by'            => session()->get('id'), 
            'assigned_to'           => session()->get('id'),
            // ------------------------------------------

            'created_at'            => date('Y-m-d H:i:s'),
            'cat_tipo_venta_id'     => $this->request->getPost('cat_tipo_venta_id'),
            'comisionista_id'       => $this->request->getPost('comisionista_id'),
        ];
        
        
        $db->table('prospects')->insert($dataProspect);
        $prospectId = $db->insertID(); 

        // 2. Insertar en FINANCIAL_METRICS
        $ingreso = floatval($this->request->getPost('total_income_charged'));
        $costos  = floatval($this->request->getPost('direct_costs')) + floatval($this->request->getPost('indirect_costs'));
        $margen  = ($ingreso > 0) ? (($ingreso - $costos) / $ingreso) * 100 : 0;

        $dataFinance = [
            'prospect_id'          => $prospectId,
            'total_income_charged' => $ingreso,
            'commission_percent'   => $this->request->getPost('commission_percent') ?: 0,
            'direct_costs'         => $this->request->getPost('direct_costs') ?: 0,
            'indirect_costs'       => $this->request->getPost('indirect_costs') ?: 0,
            'real_margin_percent'  => $margen
        ];
        $db->table('financial_metrics')->insert($dataFinance);

        // 3. Insertar en DECISION_PROFILE
        $dataDecision = [
            'prospect_id'           => $prospectId,
            'first_contact_role'    => $this->request->getPost('first_contact_role'),
            'decision_maker_role'   => $this->request->getPost('decision_maker_role'),
            'psychological_profile' => $this->request->getPost('psychological_profile') ?: 'Otro'
        ];
        $db->table('decision_profile')->insert($dataDecision);

        // 4. Insertar en ANALYSIS_QUALITATIVE
        $dataQualitative = [
            'prospect_id'          => $prospectId,
            'general_observations' => $this->request->getPost('general_observations')
        ];
        $db->table('analysis_qualitative')->insert($dataQualitative);

        return redirect()->to('/prospectos')->with('msg', 'Prospecto guardado exitosamente.');
    }

    // FORMULARIO EDITAR
    public function editar($id)
    {

    
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $db = \Config\Database::connect();

        // 1. OBTENER EL PROSPECTO
        $prospect = $db->table('prospects')
     ->select('prospects.*, 
               financial_metrics.*, 
               decision_profile.*, 
               analysis_qualitative.*,
               prospects.id as id') // <--- AGREGA ESTA LÍNEA EXACTA AL FINAL
     ->join('financial_metrics', 'financial_metrics.prospect_id = prospects.id', 'left')
     ->join('decision_profile', 'decision_profile.prospect_id = prospects.id', 'left')
     ->join('analysis_qualitative', 'analysis_qualitative.prospect_id = prospects.id', 'left')
     ->where('prospects.id', $id)
     ->get()->getRowArray();
        
        if (!$prospect) {
            return redirect()->to('/prospectos')->with('error', 'Prospecto no encontrado');
        }

        // 2. OBTENER CATALOGOS
        // Cargar nuevos catálogos
        $tipos_ventas = $db->table('cat_tipos_ventas')->where('estatus', 1)->orderBy('nombre', 'ASC')->get()->getResultArray();
        $socios = $db->table('socios')->orderBy('nombre', 'ASC')->get()->getResultArray();
        $categorias = $db->table('categorias_macro')->orderBy('nombre_categoria', 'ASC')->get()->getResultArray();
        $servicios  = $db->table('service_types')->orderBy('name', 'ASC')->get()->getResultArray();
        $estados    = $db->table('states')->orderBy('name', 'ASC')->get()->getResultArray();

        // 3. IDENTIFICAR CATEGORÍA ACTUAL (CORREGIDO)
        // Antes buscábamos un "Giro", pero ahora industry_id guarda directamente la Categoría Macro.
        // Así que pasamos el valor directo.
        $currentMacroId = $prospect['industry_id'];
        if (!empty($prospect['industry_id'])) {
            $giro = $db->table('giros')->where('id', $prospect['industry_id'])->get()->getRow();
            $currentMacroId = $giro ? $giro->categoria_id : $prospect['industry_id'];
        }

        // 4. CÁLCULO DE INTELIGENCIA
        
        // (A) Macro Categoría
        $sqlMacro = "
            SELECT m.id as macro_id, COUNT(p.id) as total_cerrados, SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM categorias_macro m
            JOIN giros g ON g.categoria_id = m.id
            JOIN prospects p ON p.industry_id = g.id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            GROUP BY m.id
        ";
        $statsMacro = $db->query($sqlMacro)->getResultArray();
        $pesosMacro = [];
        foreach($statsMacro as $stat) {
            if($stat['total_cerrados'] > 0) $pesosMacro[$stat['macro_id']] = round(($stat['ganados'] / $stat['total_cerrados']) * 100);
        }

        // (B) Decisor
        $sqlDecisor = "
            SELECT d.decision_maker_role, COUNT(p.id) as total, SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM prospects p
            JOIN decision_profile d ON p.id = d.prospect_id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            AND d.decision_maker_role IS NOT NULL GROUP BY d.decision_maker_role
        ";
        $statsDecisor = $db->query($sqlDecisor)->getResultArray();
        $pesosDecisor = [];
        foreach($statsDecisor as $stat) {
            if($stat['total'] > 0) $pesosDecisor[$stat['decision_maker_role']] = round(($stat['ganados'] / $stat['total']) * 100);
        }

        // (C) Servicio (NUEVO)
        $sqlServicio = "
            SELECT s.id as service_id, COUNT(p.id) as total_cerrados, SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM service_types s
            JOIN prospects p ON p.service_type_id = s.id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            GROUP BY s.id
        ";
        $statsServicio = $db->query($sqlServicio)->getResultArray();
        $pesosServicio = [];
        foreach($statsServicio as $stat) {
            if($stat['total_cerrados'] > 0) $pesosServicio[$stat['service_id']] = round(($stat['ganados'] / $stat['total_cerrados']) * 100);
        }

        // 6. CÁLCULO DE VIABILIDAD POR ESTADO (UBICACIÓN) 🧠
        $sqlEstado = "
            SELECT 
                e.id as state_id,
                COUNT(p.id) as total_cerrados,
                SUM(CASE WHEN p.pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM states e
            JOIN prospects p ON p.state_id = e.id
            WHERE p.pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
            GROUP BY e.id
        ";
        $statsEstado = $db->query($sqlEstado)->getResultArray();

        $pesosEstado = [];
        foreach($statsEstado as $stat) {
            if($stat['total_cerrados'] > 0) {
                $winRate = ($stat['ganados'] / $stat['total_cerrados']) * 100;
                $pesosEstado[$stat['state_id']] = round($winRate);
            }
        }

        

        // FUSIONAR PARA LA CAMPANA
        $data = array_merge($this->viewData, [
            'prospect'       => $prospect,
            'categorias'     => $categorias,
            'servicios'      => $servicios,
            'estados'        => $estados,
            'currentMacroId' => $currentMacroId ?? null,
            'pesosMacro'     => $pesosMacro ?? [],
            'pesosDecisor'   => $pesosDecisor ?? [],
            'pesosServicio'  => $pesosServicio ?? [],
            'pesosEstado'    => $pesosEstado ?? [],
            'tipos_ventas' => $tipos_ventas,
            'socios'       => $socios
        ]);

        return view('prospectos/formulario_editar', $data);
    }

    // ACTUALIZAR (UPDATES SEPARADOS)
    public function actualizar($id)
    {

        $db = \Config\Database::connect();

        // 1. A. Actualizar tabla PROSPECTS
        $dataProspect = [
            'company_name'          => $this->request->getPost('company_name'),
            'industry_id'           => $this->request->getPost('industry_id'),
            'service_type_id'       => $this->request->getPost('service_type_id'),
            'state_id'              => $this->request->getPost('state_id'),
            'employees_count'       => $this->request->getPost('employees_count'),
            'classification_group'  => $this->request->getPost('classification_group'),
            'ticket_value'          => $this->request->getPost('ticket_value'),
            'complexity_level'      => $this->request->getPost('complexity_level'),
            'duration_months'       => $this->request->getPost('duration_months'),
            'pipeline_stage'        => $this->request->getPost('pipeline_stage'),
            // NUEVO: Guardamos la fecha que el usuario seleccionó manualmente
            'last_stage_change_date' => $this->request->getPost('stage_change_date'), 
            'updated_at'            => date('Y-m-d H:i:s'),
            'cat_tipo_venta_id'     => empty($this->request->getPost('cat_tipo_venta_id')) ? null : $this->request->getPost('cat_tipo_venta_id'),
            'comisionista_id'       => empty($this->request->getPost('comisionista_id')) ? null : $this->request->getPost('comisionista_id'),
        ];

        // Disparamos el UPDATE a la base de datos
        $db->table('prospects')->where('id', $id)->update($dataProspect);

        // 2. Registramos el evento histórico en la tabla 'pipeline_events'
        $eventData = [
            'prospect_id'    => $id,
            'stage'          => $this->request->getPost('pipeline_stage'),
            'event_date'     => $this->request->getPost('stage_change_date'), // La fecha manual que capturamos
            'created_at'     => date('Y-m-d H:i:s')
        ];
        $db->table('pipeline_events')->insert($eventData);

        // =======================================================
        // NUEVO: REGISTRAR CITA EN TABLA HISTÓRICA SI APLICA
        // =======================================================
        $stage = $this->request->getPost('pipeline_stage');
        if ($stage == 'Contactado' || $stage == 'Contactado Pendiente') {
            
            $asistentes = $this->request->getPost('asistentes_cita');
            $asistentes_str = !empty($asistentes) ? implode(', ', $asistentes) : 'Ninguno';

            $dataCita = [
                'prospect_id'        => $id,
                'company_name'       => $this->request->getPost('cita_company_name'),
                'giro'               => $this->request->getPost('cita_giro'),
                'company_size'       => $this->request->getPost('cita_company_size'),
                'attendee_name'      => $this->request->getPost('cita_attendee_name'),
                'attendee_position'  => $this->request->getPost('cita_attendee_position'),
                'website'            => $this->request->getPost('cita_website'),
                'context'            => $this->request->getPost('cita_context'),
                'internal_attendees' => $asistentes_str,
                'created_at'         => date('Y-m-d H:i:s')
            ];

            $db->table('citas_prospecto')->insert($dataCita);
        }
        // =======================================================

        // B. Actualizar FINANCIAL_METRICS
        $ingreso = floatval($this->request->getPost('total_income_charged'));
        $costos  = floatval($this->request->getPost('direct_costs')) + floatval($this->request->getPost('indirect_costs'));
        $margen  = ($ingreso > 0) ? (($ingreso - $costos) / $ingreso) * 100 : 0;

        $dataFinance = [
            'total_income_charged' => $ingreso,
            'commission_percent'   => $this->request->getPost('commission_percent'),
            'direct_costs'         => $this->request->getPost('direct_costs'),
            'indirect_costs'       => $this->request->getPost('indirect_costs'),
            'real_margin_percent'  => $margen
        ];
        $this->updateOrInsert($db, 'financial_metrics', $id, $dataFinance);

        // C. Actualizar DECISION_PROFILE (código omitido para brevedad, mantener igual)
        // D. Actualizar ANALYSIS_QUALITATIVE (código omitido para brevedad, mantener igual)

        // =======================================================
        // NUEVA AUTOMATIZACIÓN: ALERTA DE CIERRE GANADO
        // =======================================================
        if ($this->request->getPost('pipeline_stage') == 'Cerrado Ganado') {
            
            // Consultamos el nombre del servicio para el correo
            $servicio = $db->table('service_types')
                        ->where('id', $dataProspect['service_type_id'])
                        ->get()->getRow();
            
                        $email = \Config\Services::email();
    
                        // Forzamos los ajustes de Mailtrap aquí mismo para asegurar
                        $config['protocol']   = 'smtp';
                        $config['SMTPHost']   = 'sandbox.smtp.mailtrap.io';
                        $config['SMTPUser']   = '37d3592c25d4e5';
                        $config['SMTPPass']   = 'c0858d9ffc1504';
                        $config['SMTPPort']   = 587;
                        $config['SMTPCrypto'] = 'tls';
                        $config['mailType']   = 'html'; // Para que el <h2> y <ul> se vean bien
                    
                        $email->initialize($config); // Aplicamos la configuración
                    
                        $email->setFrom('crm@forhumancapital.mx', 'CRM System'); 
                        $email->setTo('migueldelatorre@gmail.com'); 
                        $email->setSubject('¡CIERRE EXITOSO! - ' . $dataProspect['company_name']);
            
            $body = "<h2>🎉 ¡Nueva Venta Confirmada!</h2>";
            $body .= "<p>Se ha actualizado el estatus de un prospecto a <strong>Cerrado Ganado</strong>.</p>";
            $body .= "<hr>";
            $body .= "<ul>";
            $body .= "<li><strong>Empresa:</strong> " . $dataProspect['company_name'] . "</li>";
            $body .= "<li><strong>Servicio:</strong> " . ($servicio->name ?? 'N/A') . "</li>";
            $body .= "<li><strong>Valor del Ticket:</strong> $" . number_format($dataProspect['ticket_value'], 2) . "</li>";
            $body .= "<li><strong>Ingreso Real:</strong> $" . number_format($ingreso, 2) . "</li>";
            $body .= "</ul>";
            $body .= "<p>Favor de iniciar el proceso de facturación y onboarding.</p>";

            $email->setMessage($body);
            
            // Intentar enviar (si falla la config de correo, que no truene el sistema)
            try {
                if (!$email->send()) {
                    // Esto detendrá el sistema y te mostrará el error real de Mailtrap
                    echo $email->printDebugger(['headers', 'subject', 'body']);
                    exit; 
                }
            } catch (\Exception $e) {
                echo "Error de excepción: " . $e->getMessage();
                exit;
            }
        }
        // =======================================================

        return redirect()->to('/prospectos')->with('msg', 'Prospecto actualizado correctamente.');
    }

    // Helper privado
    private function updateOrInsert($db, $table, $prospectId, $data)
    {
        $exists = $db->table($table)->where('prospect_id', $prospectId)->countAllResults();
        if ($exists > 0) {
            $db->table($table)->where('prospect_id', $prospectId)->update($data);
        } else {
            $data['prospect_id'] = $prospectId;
            $db->table($table)->insert($data);
        }
    }

    // =======================================================
    // VISTA PERFIL Y MURO DE ACTIVIDADES (FASE 1)
    // =======================================================
    public function perfil($id)
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $db = \Config\Database::connect();

        // 1. Obtener datos básicos del prospecto
        $prospecto = $db->table('prospects')
            ->select('prospects.*, service_types.name as service_name')
            ->join('service_types', 'service_types.id = prospects.service_type_id', 'left')
            ->where('prospects.id', $id)
            ->get()->getRowArray();
            
        if (!$prospecto) {
            return redirect()->to('/prospectos')->with('error', 'Prospecto no encontrado');
        }

        // 2. Cargar el historial de actividades
        $activityModel = new \App\Models\ActivityModel();
        $actividades = $activityModel->getHistorialByProspect($id);

        // 3. Cargar los documentos
        $docModel = new \App\Models\DocumentModel();
        $documentos = $docModel->getDocsByProspect($id);

        // --- 4. CARGAR EL HISTORIAL DE CITAS (NUEVO) ---
        $citasHistorial = $db->table('citas_prospecto')
            ->where('prospect_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
        // ----------------------------------------------

        // FUSIONAR PARA LA VISTA
        $data = array_merge($this->viewData, [
            'prospect'       => $prospecto,
            'actividades'    => $actividades,
            'documentos'     => $documentos,
            'citasHistorial' => $citasHistorial // <-- Pasamos las citas a la vista
        ]);

        return view('prospectos/perfil', $data);
    }

    // =======================================================
    // ACCIONES DE ACTIVIDADES (FASE 1)
    // =======================================================

    // GUARDAR NOTA O TAREA
    public function guardarActividad()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $activityModel = new \App\Models\ActivityModel();

        $prospectId = $this->request->getPost('prospect_id');
        $tipo       = $this->request->getPost('activity_type');
        $asunto     = trim($this->request->getPost('subject'));
        $notas      = trim($this->request->getPost('notes'));
        $fechaVenc  = $this->request->getPost('due_date');

        // Lógica de Tarea Pendiente: 
        // Si el tipo es "Tarea" y pusieron fecha límite, la marcamos como "No Completada" (0)
        // Todo lo demás (Notas, llamadas ya hechas) se guarda como "Completado" (1)
        $isCompleted = 1; 
        if ($tipo == 'Tarea' && !empty($fechaVenc)) {
            $isCompleted = 0;
        }

        $data = [
            'prospect_id'   => $prospectId,
            'user_id'       => session()->get('id'), // El ID del vendedor que escribe
            'activity_type' => $tipo,
            'subject'       => empty($asunto) ? "Registro de $tipo" : $asunto,
            'notes'         => $notas,
            'due_date'      => empty($fechaVenc) ? null : $fechaVenc,
            'is_completed'  => $isCompleted
        ];

        if ($activityModel->insert($data)) {
            return redirect()->back()->with('msg', 'Actividad registrada correctamente.');
        } else {
            return redirect()->back()->with('error', 'Error al guardar la actividad.');
        }
    }

    // MARCAR TAREA COMO COMPLETADA ✅
    public function completarActividad($idActividad)
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $activityModel = new \App\Models\ActivityModel();
        
        if($activityModel->update($idActividad, ['is_completed' => 1])) {
            return redirect()->back()->with('msg', 'Tarea marcada como completada.');
        } else {
            return redirect()->back()->with('error', 'No se pudo actualizar la tarea.');
        }
    }

    public function subirDocumento()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $file = $this->request->getFile('prospect_file');
        $categoria = $this->request->getPost('category') ?? 'Otros'; 
        $prospectId = $this->request->getPost('prospect_id');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            // Guardamos en la carpeta de prospectos
            $file->move(FCPATH . 'uploads/prospectos', $newName);

            $db = \Config\Database::connect();
            $data = [
                'prospect_id'   => $prospectId,
                'user_id'       => session()->get('id'),
                'category'      => $categoria,
                'original_name' => $file->getClientName(),
                'file_name'     => $newName,
                'file_type'     => $file->getClientExtension(),
                'created_at'    => date('Y-m-d H:i:s')
            ];

            $db->table('prospect_documents')->insert($data);
            return redirect()->back()->with('msg', 'Archivo clasificado y guardado.');
        }

        return redirect()->back()->with('error', 'No se pudo subir el archivo.');
    }

    public function exportarPDF($id)
    {
        $db = \Config\Database::connect();
        $prospecto = $db->table('prospects')
            ->select('prospects.*, financial_metrics.*, service_types.name as service_name')
            ->join('financial_metrics', 'financial_metrics.prospect_id = prospects.id', 'left')
            ->join('service_types', 'service_types.id = prospects.service_type_id', 'left')
            ->where('prospects.id', $id)->get()->getRowArray();

        $dompdf = new \Dompdf\Dompdf();
        // Cargamos una vista normal de CI4 y la pasamos a HTML
        $html = view('prospectos/pdf_cotizacion', ['p' => $prospecto]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Descarga automática
        return $dompdf->stream("Cotizacion_" . $prospecto['company_name'] . ".pdf");
    }

    public function eliminarDocumento($id)
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');

        $db = \Config\Database::connect();
        $doc = $db->table('prospect_documents')->where('id', $id)->get()->getRow();

        if ($doc) {
            // 1. Borrar el archivo físico de la carpeta uploads
            $path = FCPATH . 'uploads/prospectos/' . $doc->file_name;
            if (file_exists($path)) {
                unlink($path);
            }

            // 2. Borrar el registro de la base de datos
            $db->table('prospect_documents')->where('id', $id)->delete();

            return redirect()->back()->with('msg', 'Documento eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'No se encontró el documento.');
    }

    public function revisarAbandono()
    {
        $db = \Config\Database::connect();
        
        // Buscamos prospectos Clase A sin actividad en los últimos 7 días
        $hoy = date('Y-m-d H:i:s');
        $haceSieteDias = date('Y-m-d H:i:s', strtotime('-7 days'));

        $olvidados = $db->table('prospects p')
            ->select('p.company_name, p.classification_group, MAX(a.created_at) as ultima_nota')
            ->join('prospect_activities a', 'a.prospect_id = p.id', 'left')
            ->where('p.classification_group', 'A')
            ->where('p.pipeline_stage !=', 'Cerrado Ganado')
            ->groupBy('p.id')
            ->having('ultima_nota <', $haceSieteDias)
            ->orHaving('ultima_nota IS NULL')
            ->get()->getResultArray();

        if (!empty($olvidados)) {
            $email = \Config\Services::email();
            $email->setTo('migueldelatorre@gmail.com');
            $email->setSubject('⚠️ ALERTA: Prospectos Clase A sin seguimiento');
            
            $html = "<h3>Los siguientes prospectos TOP no han tenido actividad en 7 días:</h3><ul>";
            foreach ($olvidados as $o) {
                $fecha = $o['ultima_nota'] ? date('d/m/Y', strtotime($o['ultima_nota'])) : 'Nunca';
                $html .= "<li><strong>{$o['company_name']}</strong> (Última nota: $fecha)</li>";
            }
            $html .= "</ul><p>Favor de asignar seguimiento urgente.</p>";

            $email->setMessage($html);
            $email->send();
            
            return "Alerta enviada para " . count($olvidados) . " prospectos.";
        }

        return "Todo al día, no hay prospectos olvidados.";
    }
}