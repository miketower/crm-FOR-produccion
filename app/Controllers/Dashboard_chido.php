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
        
        // 1. 📅 CAPTURAMOS FECHAS DINÁMICAS (Por defecto: Mes en curso)
        $start = $this->request->getGet('start') ?? date('Y-m-01');
        $end   = $this->request->getGet('end')   ?? date('Y-m-t'); // 't' obtiene el último día del mes
        
        $anioActual = date('Y', strtotime($end));

        // 2. 🧮 CALCULAMOS LOS PERIODOS ("YYYY-MM") EN EL RANGO
        $periodosFiltro = [];
        $currentDate = strtotime(date('Y-m-01', strtotime($start)));
        $endDate     = strtotime(date('Y-m-01', strtotime($end)));
        
        while ($currentDate <= $endDate) {
            $periodosFiltro[] = date('Y-m', $currentDate);
            $currentDate = strtotime('+1 month', $currentDate);
        }

        $titulo       = "Dashboard Comercial";
        $filtroInicio = date('d/m/Y', strtotime($start));
        $filtroFin    = date('d/m/Y', strtotime($end));

        // ==========================================
        // 🔍 LÓGICA: ¿ESTÁN CERRADOS TODOS LOS MESES SELECCIONADOS?
        // ==========================================
        $cierreQuery = $db->table('ventas_flujos')
            ->select('id')
            ->whereIn('periodo', $periodosFiltro)
            ->where('estatus', 1)
            ->limit(1)
            ->get()
            ->getRow();

        $mesEstaCerrado = !empty($cierreQuery);

        // Inicializamos arreglos
        $ingresoBrutoCanal  = [0.0, 0.0, 0.0];
        $totalValidadoCanal = [0.0, 0.0, 0.0];
        $comisionCanal      = [0.0, 0.0, 0.0];
        $totalIngresoNuevo  = 0.0;

        if ($mesEstaCerrado || count($periodosFiltro) > 1) {
            // ==========================================
            // 🚨 EXTRACCIÓN FINANCIERA (MES CERRADO O RANGOS HISTÓRICOS)
            // ==========================================
            $ventasDb = $db->table('ventas_flujos')
                ->select('cat_tipo_venta_id, SUM(ingreso_bruto_total) as total_bruto, SUM(total_validado_facturacion) as total_validado, SUM(comision_bruta) as total_comision')
                ->whereIn('periodo', $periodosFiltro)
                ->where('estatus', 1)
                ->groupBy('cat_tipo_venta_id')
                ->get()
                ->getResultArray();

            foreach ($ventasDb as $v) {
                $idx = ((int)$v['cat_tipo_venta_id']) - 1;
                if ($idx >= 0 && $idx <= 2) {
                    $ingresoBrutoCanal[$idx]  = (float)$v['total_bruto'];
                    $totalValidadoCanal[$idx] = (float)$v['total_validado'];
                    $comisionCanal[$idx]      = (float)$v['total_comision'];
                }
            }

            // ==========================================
            // CÁLCULO DE INGRESO NUEVO (MES CERRADO)
            // Condición: Prospecto creado en el año actual + Etapa "Cerrado Ganado"
            // ==========================================
            $ingresoNuevoQuery = $db->table('ventas_flujos')
                ->select('SUM(ventas_flujos.total_validado_facturacion) as ingreso_nuevo')
                ->join('prospects', 'prospects.id = ventas_flujos.prospect_id')
                ->where('YEAR(prospects.created_at)', $anioActual) // Creado en el año en curso
                ->where('prospects.pipeline_stage', 'Cerrado Ganado') // <--- NUEVA CONDICIÓN
                ->whereIn('ventas_flujos.periodo', $periodosFiltro) 
                ->where('ventas_flujos.estatus', 1)
                ->get()
                ->getRow();

            $totalIngresoNuevo = (float)($ingresoNuevoQuery->ingreso_nuevo ?? 0);

        } else {
            // ==========================================
            // 🟢 EXTRACCIÓN FINANCIERA (MES EN CURSO / SEMANAL)
            // ==========================================
            $titulo = $titulo . " (Preliminar Semanal)";

            $ventasSemanales = $db->table('cortes_semanales')
                ->select('cat_tipo_venta_id, SUM(ib_total) as total_bruto, SUM(facturacion) as total_validado')
                ->whereIn('periodo', $periodosFiltro)
                ->groupBy('cat_tipo_venta_id')
                ->get()
                ->getResultArray();

            foreach ($ventasSemanales as $v) {
                $idx = ((int)$v['cat_tipo_venta_id']) - 1;
                if ($idx >= 0 && $idx <= 2) {
                    $ingresoBrutoCanal[$idx]  = (float)$v['total_bruto'];
                    $totalValidadoCanal[$idx] = (float)$v['total_validado'];
                }
            }

            $ingresoNuevoSemanales = $db->table('cortes_semanales')
                ->selectSum('ib_nuevo')
                ->whereIn('periodo', $periodosFiltro)
                ->get()
                ->getRow();

            $totalIngresoNuevo = (float)($ingresoNuevoSemanales->ib_nuevo ?? 0);
        }

        // Totales macro
        $ingresoBrutoTotal  = array_sum($ingresoBrutoCanal);
        $totalValidadoTotal = array_sum($totalValidadoCanal);
        $totalComisionMes   = array_sum($comisionCanal); 

        // ==========================================
        // 🎯 LECTURA DINÁMICA DE FORECAST (PROYECCIONES)
        // ==========================================
        
        // 1. Obtener Meta Anual Dinámica
        $forecastAnual = $db->table('forecasts_anual')
            ->where('anio', $anioActual)
            ->get()->getRow();
            
        $metaAnual = (float)($forecastAnual->meta_anual ?? 0); 
        // Usamos una variable auxiliar para evitar división entre cero en las matemáticas si no han capturado la meta
        $metaMatematica = $metaAnual > 0 ? $metaAnual : 1; 

        // 2. Obtener Proyecciones de Actividad (Solo de los meses seleccionados en el filtro)
        $mesesNumeros = array_map(function($p) { 
            return (int)explode('-', $p)[1]; 
        }, $periodosFiltro);

        $forecastAct = $db->table('forecasts_mensual')
            ->select('SUM(citas) as total_citas, SUM(propuestas) as total_propuestas, SUM(cierres) as total_cierres')
            ->where('anio', $anioActual)
            ->whereIn('mes', $mesesNumeros)
            ->get()->getRow();

        // REVISA QUE ESTAS TRES LÍNEAS ESTÉN AQUÍ:
        $proyectadoCitas      = (int)($forecastAct->total_citas ?? 0);
        $proyectadoPropuestas = (int)($forecastAct->total_propuestas ?? 0);
        $proyectadoCierres    = (int)($forecastAct->total_cierres ?? 0);

        // --- CÁLCULO DINÁMICO DEL ACUMULADO ANUAL TOTAL (YTD) BASADO EN INGRESO BRUTO ---
        $acumuladoAnualIngresoBrutoReal = (float)$db->table('ventas_flujos')
            ->where('estatus', 1)
            ->like('periodo', $anioActual . '-', 'after')
            ->selectSum('ingreso_bruto_total')
            ->get()
            ->getRow()->ingreso_bruto_total;

        // Evitar duplicar si es consulta histórica. Se suma el mes en curso si no está cerrado
        $acumuladoAnualDona = $mesEstaCerrado ? $acumuladoAnualIngresoBrutoReal : ($acumuladoAnualIngresoBrutoReal + $ingresoBrutoTotal); // <--- Usando $ingresoBrutoTotal

        // Porcentajes de avance para las donas radiales (usando Ingreso Bruto vs Meta)
        $porcentajeMetaAnual = ($ingresoBrutoTotal > 0) ? round(($ingresoBrutoTotal / $metaMatematica) * 100, 1) : 0;
        $pctAcumulado        = ($acumuladoAnualDona > 0) ? round(($acumuladoAnualDona / $metaMatematica) * 100, 1) : 0;

        // Peso específico de cada canal comercial en relación a la meta (basado en Ingreso Bruto)
        $contribucionCanales = [
            $ingresoBrutoTotal > 0 ? round(($ingresoBrutoCanal[0] / $metaMatematica) * 100, 2) : 0, 
            $ingresoBrutoTotal > 0 ? round(($ingresoBrutoCanal[1] / $metaMatematica) * 100, 2) : 0, 
            $ingresoBrutoTotal > 0 ? round(($ingresoBrutoCanal[2] / $metaMatematica) * 100, 2) : 0
        ];

        // ==========================================
        // 📊 EVENTOS Y EFECTIVIDAD (Pipeline)
        // ==========================================
        
        // 1. Extraemos los eventos basados en el TIPO DE VENTA
        $eventosDb = $db->table('pipeline_events')
            ->select('prospects.cat_tipo_venta_id, pipeline_events.stage, COUNT(*) as total') 
            ->join('prospects', 'prospects.id = pipeline_events.prospect_id')
            ->whereIn('DATE_FORMAT(pipeline_events.event_date, "%Y-%m")', $periodosFiltro)
            ->groupBy('prospects.cat_tipo_venta_id, pipeline_events.stage') 
            ->get()->getResultArray();

        $citas = [0, 0, 0];
        $propuestas = [0, 0, 0];
        $cierres = [0, 0, 0];

        foreach ($eventosDb as $ev) {
            // Asignamos a la cubeta correcta (0=Opt, 1=Sol, 2=Otros)
            $idx = (int)$ev['cat_tipo_venta_id'] - 1; 
            
            if ($idx >= 0 && $idx <= 2) {
                if ($ev['stage'] == 'Contactado')     $citas[$idx]      += (int)$ev['total'];
                if ($ev['stage'] == 'Propuesta')      $propuestas[$idx] += (int)$ev['total'];
                if ($ev['stage'] == 'Cerrado Ganado') $cierres[$idx]    += (int)$ev['total'];
            }
        }

        // 2. Calculamos la efectividad histórica basada en el TIPO DE VENTA
        $sqlEfectividad = "SELECT cat_tipo_venta_id,
        SUM(CASE WHEN pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados,
        COUNT(*) as total
        FROM prospects 
        WHERE pipeline_stage IN ('Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')
        GROUP BY cat_tipo_venta_id"; 
        
        $efectividadRaw = $db->query($sqlEfectividad)->getResultArray();
        $dataEfectividad = [0, 0, 0]; 

        foreach ($efectividadRaw as $e) {
            $idx = (int)$e['cat_tipo_venta_id'] - 1; 
            if ($idx >= 0 && $idx <= 2 && $e['total'] > 0) {
                $dataEfectividad[$idx] = round(($e['ganados'] / $e['total']) * 100);
            }
        }

        // ==========================================
        // 📊 MAPEADO FINAL A LA VISTA
        // ==========================================
        $proyectadoFinanciero = ($metaMatematica / 12) * count($periodosFiltro);
        $data['start']              = $start; // Pasamos las fechas a la vista
        $data['end']                = $end;
        $data['titulo']             = $titulo;
        $data['filtroInicio']       = $filtroInicio;
        $data['filtroFin']          = $filtroFin;
        $data['ingresoBrutoTotal']  = $ingresoBrutoTotal;
        $data['totalValidadoTotal'] = $totalValidadoTotal;
        
        $data['dataMontosVs'] = [$ingresoBrutoTotal, $totalValidadoTotal, $totalIngresoNuevo, $proyectadoFinanciero];
        $data['canalesLabels']          = ['Optimización', 'Soluciones', 'Otros'];
        $data['dataIngresoBrutoCanal']  = $ingresoBrutoCanal;
        $data['dataTotalValidadoCanal'] = $totalValidadoCanal;
        $data['contribucionCanales']    = $contribucionCanales;
        
        $data['dataCitas']           = $citas;
        $data['dataPropuestas']      = $propuestas;
        $data['dataCierres']         = $cierres;
        $data['dataRealAct'] = [array_sum($citas), array_sum($propuestas), array_sum($cierres)];
        // Cambia la línea de dataProyectadoAct por esto:
        $data['dataProyectadoAct'] = [$proyectadoCitas, $proyectadoPropuestas, $proyectadoCierres];
        $data['dataEfectividad']     = $dataEfectividad; 
        
        $data['porcentajeMetaAnual'] = $porcentajeMetaAnual;
        $data['pctAcumulado']        = $pctAcumulado;

        return view('dashboard/home', $data);
    }

    // --- Pipeline --- //

    public function pipeline()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();

        // 1. 📅 CAPTURAMOS RANGO DE FECHAS (Por defecto: Año en curso completo)
        $start = $this->request->getGet('start') ?? date('Y-01-01');
        $end   = $this->request->getGet('end')   ?? date('Y-12-31');

        // 2. Consulta de prospectos filtrada por rango de creación
        $prospectos = $db->table('prospects')
                        ->select('id, company_name, pipeline_stage, ticket_value')
                        ->where('created_at >=', $start . ' 00:00:00')
                        ->where('created_at <=', $end . ' 23:59:59')
                        ->where('deleted_at', null)
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();

        // 3. Preparación de datos para la vista
        $anioActual = date('Y', strtotime($end));
        $totalAnualQuery = $db->table('prospects')
                            ->selectSum('ticket_value')
                            ->where('YEAR(created_at)', $anioActual)
                            ->where('deleted_at', null)
                            ->get()
                            ->getRow();

        // Preparación de datos para la vista
        $data = [
            'prospectos'          => $prospectos,
            'titulo'              => 'Pipeline de Ventas',
            'start'               => $start,
            'end'                 => $end,
            'totalAnualPipeline'  => (float)($totalAnualQuery->ticket_value ?? 0) // <--- Nueva variable
        ];

        return view('dashboard/pipeline', $data);
    }

    public function updateStage()
    {
        // 1. Validamos que la petición sea AJAX por seguridad
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Acceso denegado']);
        }

        $db = \Config\Database::connect();
        
        // 2. Obtenemos los datos enviados desde el JavaScript (ID del prospecto y nueva etapa)
        $id       = $this->request->getPost('id');
        $newStage = $this->request->getPost('stage');
        
        // Fecha actual del momento del Drag and Drop
        $fechaActualYMD = date('Y-m-d');
        $fechaActualHIS = date('Y-m-d H:i:s');

        $db->transStart();

        // 3. Actualizamos el prospecto con su nueva etapa y su última fecha de cambio
        $db->table('prospects')
           ->where('id', $id)
           ->update([
               'pipeline_stage'         => $newStage,
               'last_stage_change_date' => $fechaActualYMD, // Guardamos la fecha actual en la tabla padre
               'updated_at'             => $fechaActualHIS
           ]);

        // 4. Insertamos el evento histórico en la tabla 'pipeline_events' (idéntico al módulo de perfilamiento)
        $db->table('pipeline_events')->insert([
            'prospect_id' => $id,
            'stage'       => $newStage,
            'event_date'  => $fechaActualYMD, // Fecha operativa
            'created_at'  => $fechaActualHIS  // Timestamp del sistema
        ]);

        $db->transComplete();

        // 5. Respondemos al frontend
        if ($db->transStatus() === TRUE) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Etapa e histórico actualizados correctamente']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'No se pudo actualizar el estado']);
        }
    }
}