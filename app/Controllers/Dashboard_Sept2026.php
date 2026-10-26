<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        // REDIRECCIÓN AUTOMÁTICA PARA CAPTURADORES
        $emailsCaptura = [
            'danya.cruz@forhumancapital.mx',
            'samuel.nunez@theblancgroup.com',
            'alicia.gutierrez@forhumancapital.mx'
        ];

        if (in_array(session()->get('email'), $emailsCaptura)) {
            return redirect()->to('/carga-comercial');
        }
        
        $db = \Config\Database::connect();
        $q = $this->request->getVar('q');
        if (!$q) { $q = '4'; }
        
        if ($q == '1') {
            $fechaInicio = '2026-01-01';
            $trimestreLabel = 'Q1';
        } elseif ($q == '2') {
            $fechaInicio = '2026-04-01';
            $trimestreLabel = 'Abril';
        } elseif ($q == '3') {
            $fechaInicio = '2026-05-01';
            $trimestreLabel = 'Mayo S1';
        } else {
            $fechaInicio = '2026-06-01';
            $trimestreLabel = 'Junio Semana 1';
        }

        $fechaFin = ($q == '3') ? '2026-05-07' : (($q == '4') ? '2026-06-07' : date('Y-m-d'));

        $canales = array('Optimización', 'Soluciones', 'Otros');
        $metaAnual = 100000000; 
        
        $q = $this->request->getGet('q') ?? '3';

        $titulo       = "Dashboard - Junio (Mes en Curso)";
        $filtroInicio = "01/06/2026";
        $filtroFin    = "30/06/2026";

        $canalesLabels = ['Optimización', 'Soluciones', 'Otros'];

        switch ((string)$q) { 
            case '1':
                $titulo       = "Dashboard";
                $filtroInicio = "01/01/2026";
                $filtroFin    = "31/03/2026";

                $citas = array(62+52, 7, 1); 
                $propuestas = array(17, 7+5, 1); 
                $cierres = array(4+10, 6, 1);
                $montoVendidoCanal = array(171585631.72, 0, 0);
                $montoCobradoCanal = array(28094288.72, 2401249, 1073753+2298370.59);
                $proyectadoAct = array(300, 120, 24); 
                $proyVenta = 50000000; 
                $ingNuevo = 173364.48+588197.93+96086+140559.5;
                break;

            case '2':
                $titulo       = "Dashboard";
                $filtroInicio = "01/04/2026";
                $filtroFin    = "30/06/2026";
                
                $citas = array(11 + 16 + 10, 6 + 0 + 0, 1 + 0 + 0); 
                $propuestas = array(4 + 5 + 2, 4 + 0 + 0, 1 + 0 + 0); 
                $cierres = array(1 + 3 + 2, 4 + 0 + 0, 0 + 0 + 0);

                $montoVendidoCanal = array(18665122 + 1709303.48 + 0 + 55695066.91, 0 + 0 + 0, 399473 + 876009.74 + 0); 
                $montoCobradoCanal = array(18665122 + 3044916.43 + 799340.23+1008493.44 + 695,051.65, 0 + 0 + 0, 399473 + 876009.74 + 1022887.85);

                $proyectadoAct = array(50 + 50 + 24+12, 20 + 20 + 10+5, 4 + 4 + 2+1); 
                $proyVenta = 5043400.00 + 5043400.00 + 0; 
                $ingNuevo = 682849.30 + 87271.88 + 10140.60 + 51332.10+329392.86055;
                break;

            case '3':
            default:
                $titulo       = "Dashboard";
                $filtroInicio = "01/01/2026";
                $filtroFin    = "31/03/2026";

                $citas = array(62+52+19+3+5+3, 7, 1); 
                $propuestas = array(17+13+1+1+2, 7+5, 1); 
                $cierres = array(4+10+3+1+2, 6, 1);
                $montoVendidoCanal = array(275378053.23+5010644.78+9964592.58+3245259.04+4565850.57+9327499.35+4587932.05+6559605.88+7028184.79, 0, 0);
                $montoCobradoCanal = array(28094288.72+804659.83+1624894.70+1624894.75+744078.64+1526197.05+1138097.34, 2401249, 11206971.96+277378.67+151113.45+188939.95+271580.83+667012.57105);
                $proyectadoAct = array(300, 120, 24); 
                $proyVenta = 100000000; 
                $ingNuevo = 998207.91+9838.77+9838.77+104794.13+329392.86055+152102.16+106452.97+66542.81+50643.97+294909.48;
                break;
        }

        // =======================================================================================
        // SUMATORIA DINÁMICA DE LA TABLA 'capturas_comerciales'
        // =======================================================================================
        $fechaInicioDinamica = '2026-09-01'; // Cambiado de agosto a septiembre

        // =======================================================================================
        // SUMATORIA DINÁMICA DE LA TABLA 'capturas_comerciales' (GLOBAL PARA TODO EL HISTORIAL)
        // =======================================================================================
        // Quitamos el filtro restrictivo de fecha >= '2026-09-01' para que sume TODAS las cargas semanales acumuladas
        $capturas = $db->table('capturas_comerciales')
            ->select('
                canal,
                SUM(citas_asociados) as total_citas_asociados,
                SUM(citas_directa)   as total_citas_directa,
                SUM(citas_eventos)   as total_citas_eventos,
                SUM(citas_prosper)   as total_citas_prosper,
                SUM(citas)           as total_citas,
                SUM(propuestas)      as total_propuestas,
                SUM(cierres)         as total_cierres,
                SUM(monto_vendido)   as total_monto_vendido,
                SUM(monto_cobrado)   as total_monto_cobrado,
                SUM(ingreso_nuevo)   as total_ingreso_nuevo
            ')
            ->where('deleted_at', null)
            ->groupBy('canal')
            ->get()
            ->getResultArray();

        $canalIndex = [
            'Optimización'  => 0,
            'Soluciones'    => 1,
            'Otros'         => 2,
            'Venta directa' => 1,
            'Asociado FOR'  => 0,
            'Prosperhouse'  => 2,
            'Evento'        => 2,
            'Venta en frío' => 0
        ];

        $totalCitasDinamico        = 0;
        $totalCitasAsociados       = 0;
        $totalCitasDirecta         = 0;
        $totalCitasEventos         = 0;
        $totalCitasProsper         = 0;

        $totalPropuestasDinamico   = 0;
        $totalCierresDinamico      = 0;
        $totalMontoCobradoDinamico = 0;
        $totalMontoVendidoDinamico = 0; // NUEVO PARA ASEGURAR ACUMULADO
        $totalIngresoNuevoDinamico = 0;

        foreach ($capturas as $c) {
            $nombreCanal = trim($c['canal']);
            
            // Si el canal existe en nuestro mapa, sumamos sus valores al índice correspondiente de las gráficas
            if (isset($canalIndex[$nombreCanal])) {
                $idx = $canalIndex[$nombreCanal];

                $citas[$idx]             += (int) $c['total_citas'];
                $propuestas[$idx]        += (int) $c['total_propuestas'];
                $cierres[$idx]           += (int) $c['total_cierres'];
                $montoVendidoCanal[$idx] += (float) $c['total_monto_vendido'];
                $montoCobradoCanal[$idx] += (float) $c['total_monto_cobrado'];
            }

            $totalCitasAsociados     += (int) $c['total_citas_asociados'];
            $totalCitasDirecta       += (int) $c['total_citas_directa'];
            $totalCitasEventos       += (int) $c['total_citas_eventos'];
            $totalCitasProsper       += (int) $c['total_citas_prosper'];

            $totalCitasDinamico        += (int) $c['total_citas'];
            $totalPropuestasDinamico   += (int) $c['total_propuestas'];
            $totalCierresDinamico      += (int) $c['total_cierres'];
            $totalMontoVendidoDinamico += (float) $c['total_monto_vendido']; // Acumulado Facturado
            $totalMontoCobradoDinamico += (float) $c['total_monto_cobrado']; // Acumulado Cobrado
            $totalIngresoNuevoDinamico += (float) $c['total_ingreso_nuevo'];
        }

        $ingNuevo += $totalIngresoNuevoDinamico;
        // =======================================================================================
        // =======================================================================================

        // CÁLCULO DE ACTIVIDADES GENERALES
        $dataRealAct = [array_sum($citas), array_sum($propuestas), array_sum($cierres)];

        // CÁLCULO DE EFECTIVIDAD
        $dataEfectividad = [];
        foreach ($cierres as $key => $valorCierre) {
            $propuesta = $propuestas[$key] ?? 0;
            $dataEfectividad[] = $propuesta > 0 ? round(($valorCierre / $propuesta) * 100, 1) : 0;
        }

        $metaAnual = 100000000;

        $montoCobradoTotal = 35557111.03 + 12762997.43 + 6950051.65 + $totalMontoCobradoDinamico;
        // $montoVendidoTotal = array_sum($montoVendidoCanal) + $totalMontoVendidoDinamico;

        $montoVendidoTotal = array_sum($montoVendidoCanal);
        
        $q1_real_cobrado = 13909811 + 301000 + 1073753; 
        $abril_mayo_real_cobrado = 18665122 + 0 + 399473;
        $junio_real_cobrado = 799340.23+1008493.44 + 0 + 1022887.85 + 8705213.36;
        $acumuladoAnualCobradoReal = $q1_real_cobrado + $abril_mayo_real_cobrado + $junio_real_cobrado + $totalMontoCobradoDinamico + '6950051.65';

        if ($q == '1') {
            $acumuladoAnualDona = $q1_real_cobrado;
        } else {
            $acumuladoAnualDona = $acumuladoAnualCobradoReal;
        }

        $porcentajeMetaAnual = ($montoCobradoTotal > 0) ? round(($montoCobradoTotal / $metaAnual) * 100, 1) : 0;
        $pctAcumulado        = ($acumuladoAnualDona > 0) ? round(($acumuladoAnualDona / $metaAnual) * 100, 1) : 0;

        $contribucionCanales = [
            round(($montoCobradoCanal[0] / $metaAnual) * 100, 2), 
            round(($montoCobradoCanal[1] / $metaAnual) * 100, 2), 
            round(($montoCobradoCanal[2] / $metaAnual) * 100, 2)
        ];

        // DATOS GENERALES PARA HOME.PHP
        $data['q']                  = $q;
        $data['titulo']             = $titulo;
        $data['filtroInicio']       = $filtroInicio;
        $data['filtroFin']          = $filtroFin;
        
        $data['montoVendidoTotal']  = array_sum($montoVendidoCanal);
        $data['montoCobradoTotal']  = $montoCobradoTotal;

        // --- CÁLCULO DE PROYECCIONES (RUN RATE) ---
        // date('z') devuelve el día del año (0 a 364), le sumamos 1 para los días reales transcurridos
        $diasTranscurridos = date('z') + 1; 
        $diasTotalesAño = date('L') ? 366 : 365;

        $ibProyectado = 0;
        $ibNuevoProyectado = 0; // NUEVA VARIABLE PARA IB NUEVO

        if ($diasTranscurridos > 0) {
            // Promedio diario * días del año
            $ibProyectado = ($montoCobradoTotal / $diasTranscurridos) * $diasTotalesAño;
            
            // Mismo cálculo aplicado al Ingreso Nuevo
            $ibNuevoProyectado = ($ingNuevo / $diasTranscurridos) * $diasTotalesAño;
        }
        
        // Se agrega $ibNuevoProyectado como el quinto elemento del arreglo
        $data['dataMontosVs']       = [
            array_sum($montoVendidoCanal), // 1. Facturado
            $montoCobradoTotal,            // 2. IB Logrado
            $ibProyectado,                 // 3. IB Proyectado
            $ingNuevo,                     // 4. IB Nuevo Logrado
            $ibNuevoProyectado,            // 5. IB Nuevo Proyectado (NUEVO)
            $proyVenta                     // 6. Proyectado
        ];
        
        $data['canalesLabels']       = $canalesLabels;
        $data['dataVendidoCanal']    = $montoCobradoCanal; 
        $data['contribucionCanales'] = $contribucionCanales;
        $data['dataCobradoCanal']    = $montoCobradoCanal; 
        
        $data['dataCitas']          = $citas;
        $data['dataPropuestas']     = $propuestas;
        $data['dataCierres']        = $cierres;
        $data['dataRealAct']        = $dataRealAct;
        $data['dataProyectadoAct']  = $proyectadoAct;
        $data['dataEfectividad']    = $dataEfectividad;
        
        $data['porcentajeMetaAnual'] = $porcentajeMetaAnual;
        $data['pctAcumulado']        = $pctAcumulado;

        // --- CÁLCULO DE LA SEMANA A MOSTRAR ---
        $semanaMostrar = 1; // Cambiado a 4
        $tituloSemanaCitas = "Citas Semana " . $semanaMostrar . " (Septiembre)";

        $distribucionCitasLabels = ['Asociados FOR', 'Venta Directa', 'Eventos', 'Prosper'];

        // 1. DATOS MENSUALES (SEPTIEMBRE COMPLETO)
        $septiembreDistribucionValores = [
            $totalCitasAsociados,
            $totalCitasDirecta,
            $totalCitasEventos,
            $totalCitasProsper
        ];
        $totalCitasSeptiembre = array_sum($septiembreDistribucionValores);

        // --- CONSULTA: Primera semana de Septiembre (Del 1 al 7) ---
        $capturasSemana = $db->table('capturas_comerciales')
            ->select('
                SUM(citas_asociados) as sem_citas_asociados,
                SUM(citas_directa)   as sem_citas_directa,
                SUM(citas_eventos)   as sem_citas_eventos,
                SUM(citas_prosper)   as sem_citas_prosper
            ')
            ->where('fecha >=', '2026-09-01')
            ->where('fecha <=', '2026-09-07')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        // 2. DATOS SEMANALES (SEMANA 1)
        $semanaDistribucionValores = [
            (int) ($capturasSemana['sem_citas_asociados'] ?? 0),
            (int) ($capturasSemana['sem_citas_directa'] ?? 0),
            (int) ($capturasSemana['sem_citas_eventos'] ?? 0),
            (int) ($capturasSemana['sem_citas_prosper'] ?? 0)
        ];
        $totalCitasSemana = array_sum($semanaDistribucionValores);

        // --- ASIGNACIÓN DE DATOS A LA VISTA ---
        $data['semanaMostrar']             = $semanaMostrar;
        $data['tituloSemanaCitas']         = $tituloSemanaCitas;

        // Variables para "Progreso Citas Septiembre 2026"
        $data['julioCitasActual']          = $totalCitasSeptiembre; // (Nota: puedes renombrar esta variable en la vista si prefieres, pero aquí la mapeamos con el total de septiembre)
        $data['julioCitasMeta']            = 50; // Meta de citas mensual
        $data['julioDistribucionLabels']   = $distribucionCitasLabels;
        $data['julioDistribucionValores']  = $septiembreDistribucionValores;

        // Variables para la Primera Semana de Septiembre
        $data['sem3CitasActual']           = $totalCitasSemana;
        $data['sem3DistribucionLabels']    = $distribucionCitasLabels;
        $data['sem3DistribucionValores']   = $semanaDistribucionValores;

        // EMBUDO / FUNNEL DE SEPTIEMBRE
        $data['julioFunnelLabels']  = ['Citas', 'Propuestas', 'Cierres'];
        $data['julioFunnelValores'] = [
            $totalCitasSeptiembre,
            $totalPropuestasDinamico,
            $totalCierresDinamico
        ];

        return view('dashboard/home', $data);
    }

    public function forzarMigracion()
    {
        $migrations = \Config\Services::migrations();
        try {
            $migrations->latest();
            return "¡Gema Actualizada! Todas las migraciones se ejecutaron con éxito en producción.";
        } catch (\Throwable $e) {
            return "Error al migrar: " . $e->getMessage();
        }
    }

    public function pipeline()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();

        $start = $this->request->getGet('start') ?? date('Y-01-01');
        $end   = $this->request->getGet('end')   ?? date('Y-12-31');

        $prospectos = $db->table('prospects')
                        ->select('id, company_name, pipeline_stage, ticket_value')
                        ->where('created_at >=', $start . ' 00:00:00')
                        ->where('created_at <=', $end . ' 23:59:59')
                        ->where('deleted_at', null)
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();

        $anioActual = date('Y', strtotime($end));
        $totalAnualQuery = $db->table('prospects')
                            ->selectSum('ticket_value')
                            ->where('YEAR(created_at)', $anioActual)
                            ->where('deleted_at', null)
                            ->get()
                            ->getRow();

        $data = [
            'prospectos'          => $prospectos,
            'titulo'              => 'Pipeline de Ventas',
            'start'               => $start,
            'end'                 => $end,
            'totalAnualPipeline'  => (float)($totalAnualQuery->ticket_value ?? 0)
        ];

        return view('dashboard/pipeline', $data);
    }

    public function updateStage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acceso denegado']);
        }

        $db = \Config\Database::connect();
        
        $id       = $this->request->getPost('id');
        $newStage = $this->request->getPost('stage');
        
        $fechaActualYMD = date('Y-m-d');
        $fechaActualHIS = date('Y-m-d H:i:s');

        $db->transStart();

        $db->table('prospects')
           ->where('id', $id)
           ->update([
               'pipeline_stage'         => $newStage,
               'last_stage_change_date' => $fechaActualYMD,
               'updated_at'             => $fechaActualHIS
           ]);

        $db->table('pipeline_events')->insert([
            'prospect_id' => $id,
            'stage'       => $newStage,
            'event_date'  => $fechaActualYMD,
            'created_at'  => $fechaActualHIS
        ]);

        $db->transComplete();

        if ($db->transStatus() === TRUE) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Etapa e histórico actualizados correctamente']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'No se pudo actualizar el estado']);
        }
    }
}