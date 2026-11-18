<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
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

        $titulo       = "Dashboard - Ejecución Anual 2026";
        $filtroInicio = "01/01/2026";
        $filtroFin    = "30/09/2026";

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
                $montoCobradoCanal = array(18665122 + 3044916.43 + 799340.23+1008493.44 + 695051.65, 0 + 0 + 0, 399473 + 876009.74 + 1022887.85);

                $proyectadoAct = array(50 + 50 + 24+12, 20 + 20 + 10+5, 4 + 4 + 2+1); 
                $proyVenta = 5043400.00 + 5043400.00 + 0; 
                $ingNuevo = 682849.30 + 87271.88 + 10140.60 + 51332.10+329392.86055;
                break;

            case '3':
            default:
                $titulo       = "Dashboard - Ejecución Anual 2026";
                $filtroInicio = "01/01/2026";
                $filtroFin    = "30/09/2026";

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
        // SUMATORIA DINÁMICA DE ACTIVIDAD 
        // =======================================================================================
        $capturas = $db->table('capturas_comerciales')
            ->select('
                canal,
                SUM(citas_asociados) as total_citas_asociados,
                SUM(citas_directa)   as total_citas_directa,
                SUM(citas_eventos)   as total_citas_eventos,
                SUM(citas_prosper)   as total_citas_prosper,
                SUM(citas)           as total_citas,
                SUM(propuestas)      as total_propuestas,
                SUM(cierres)         as total_cierres
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

        foreach ($capturas as $c) {
            $nombreCanal = trim($c['canal']);
            if (isset($canalIndex[$nombreCanal])) {
                $idx = $canalIndex[$nombreCanal];
                $citas[$idx]             += (int) $c['total_citas'];
                $propuestas[$idx]        += (int) $c['total_propuestas'];
                $cierres[$idx]           += (int) $c['total_cierres'];
            }

            $totalCitasAsociados     += (int) $c['total_citas_asociados'];
            $totalCitasDirecta       += (int) $c['total_citas_directa'];
            $totalCitasEventos       += (int) $c['total_citas_eventos'];
            $totalCitasProsper       += (int) $c['total_citas_prosper'];

            $totalCitasDinamico        += (int) $c['total_citas'];
            $totalPropuestasDinamico   += (int) $c['total_propuestas'];
            $totalCierresDinamico      += (int) $c['total_cierres'];
        }
        
        $dataRealAct = [array_sum($citas), array_sum($propuestas), array_sum($cierres)];
        $dataEfectividad = [];
        foreach ($cierres as $key => $valorCierre) {
            $propuesta = $propuestas[$key] ?? 0;
            $dataEfectividad[] = $propuesta > 0 ? round(($valorCierre / $propuesta) * 100, 1) : 0;
        }

        $metaAnual = 100000000;
        
        $data['q']                  = $q;
        $data['titulo']             = $titulo;
        $data['filtroInicio']       = $filtroInicio;
        $data['filtroFin']          = $filtroFin;
        
        // =======================================================================================
        // --- INYECCIÓN DE VALORES EXACTOS (AUDITADOS Y ACTUALIZADOS) ---
        // =======================================================================================
        
        // 1. Totales Globales (Soluciones NO suma al KPI principal de arriba)
        //$montoVendidoTotalExacto = 679192222.00; // Facturado Total Acumulado 
        //$montoCobradoTotalExacto = 79238791.70;  // Ingreso Bruto Total Acumulado (EXACTO, sin sumar Soluciones aquí)
        
        // Acumulado anterior + Optimización de octubre.
        $montoVendidoTotalExacto = 708393425.91;
        $montoCobradoTotalExacto = 82952322.13;
        // 2. Ingresos Nuevos
        //$ingNuevoExacto          = 2251660.97;   // Ingreso Bruto Nuevo Acumulado
        $ingNuevoExacto = 2428826.96;
        $ingNuevoProyectado      = 3196461.67;   // Ingreso Bruto Nuevo Total Proyectado
        
        // 3. Proyecciones Globales (Sin sumar Soluciones)
        $proyVentaExacta         = 905589629.33; // Facturación Proyectada Global 2026
        $ibProyectadoExacto      = 105651722.27; // Ingreso Bruto Proyectado Global 2026
        
        $data['montoVendidoTotal']  = $montoVendidoTotalExacto;
        $data['montoCobradoTotal']  = $montoCobradoTotalExacto;

        // Arreglo con las 6 barras principales
        $data['dataMontosVs']       = [
            $montoVendidoTotalExacto, // 1. Facturado Global
            $montoCobradoTotalExacto, // 2. IB Global Logrado
            $ibProyectadoExacto,      // 3. IB Global Proyectado
            $ingNuevoExacto,          // 4. IB Nuevo Logrado
            $ingNuevoProyectado,      // 5. IB Nuevo Proyectado
            $proyVentaExacta          // 6. Facturación Proyectada Global
        ];
        
        // =======================================================================================
        // ESTRUCTURA DE LOS 3 CANALES (Optimización, Soluciones, Otros)
        // =======================================================================================
        $data['canalesLabels']       = ['Optimización', 'Soluciones', 'Otros'];
        
        // FACTURACIÓN: Opt = 518.4M | Soluciones = 0 | Otros (Cohete+Rem+Otros) = 160.7M
        $data['dataVendidoCanal']    = [65320155.00, 3073174.00, 13918636.70]; 
        
        // INGRESO BRUTO: Aquí SÍ incluimos los 3.07M de Soluciones para que salga en su rebanada de pastel
        //$data['dataCobradoCanal']    = [65320154.59, 3073174.00, 13918639.73]; 
        $data['dataCobradoCanal'] = [
            69033685.02, // Optimización, incluyendo octubre
            3073174.00,  // Soluciones
            13918639.73  // Otros
        ];
        
        // Porcentajes de contribución (Ajustado)
        // $data['contribucionCanales'] = [65.3, 3.1, 13.9];
        $data['contribucionCanales'] = [69.0, 3.1, 13.9];
        
        $data['dataCitas']          = $citas;
        $data['dataPropuestas']     = $propuestas;
        $data['dataCierres']        = $cierres;
        $data['dataRealAct']        = $dataRealAct;
        $data['dataProyectadoAct']  = $proyectadoAct;
        $data['dataEfectividad']    = $dataEfectividad;
        
        $data['porcentajeMetaAnual'] = ($montoCobradoTotalExacto > 0) ? round(($montoCobradoTotalExacto / $metaAnual) * 100, 1) : 0;
        $data['pctAcumulado']        = $data['porcentajeMetaAnual'];

        // --- CÁLCULO DE LA SEMANA A MOSTRAR ---
        $semanaMostrar = 1; 
        $tituloSemanaCitas = "Citas Semana " . $semanaMostrar . " (Septiembre)";

        $distribucionCitasLabels = ['Asociados FOR', 'Venta Directa', 'Eventos', 'Prosper'];

        $septiembreDistribucionValores = [
            $totalCitasAsociados,
            $totalCitasDirecta,
            $totalCitasEventos,
            $totalCitasProsper
        ];
        $totalCitasSeptiembre = array_sum($septiembreDistribucionValores);

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

        $semanaDistribucionValores = [
            (int) ($capturasSemana['sem_citas_asociados'] ?? 0),
            (int) ($capturasSemana['sem_citas_directa'] ?? 0),
            (int) ($capturasSemana['sem_citas_eventos'] ?? 0),
            (int) ($capturasSemana['sem_citas_prosper'] ?? 0)
        ];
        $totalCitasSemana = array_sum($semanaDistribucionValores);

        $data['semanaMostrar']             = $semanaMostrar;
        $data['tituloSemanaCitas']         = $tituloSemanaCitas;
        $data['julioCitasActual']          = $totalCitasSeptiembre; 
        $data['julioCitasMeta']            = 50; 
        $data['julioDistribucionLabels']   = $distribucionCitasLabels;
        $data['julioDistribucionValores']  = $septiembreDistribucionValores;
        $data['sem3CitasActual']           = $totalCitasSemana;
        $data['sem3DistribucionLabels']    = $distribucionCitasLabels;
        $data['sem3DistribucionValores']   = $semanaDistribucionValores;

        $data['julioFunnelLabels']  = ['Citas', 'Propuestas', 'Cierres'];
        $data['julioFunnelValores'] = [$totalCitasSeptiembre, $totalPropuestasDinamico, $totalCierresDinamico];

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