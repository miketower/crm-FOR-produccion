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

        // ---------------------------------------------------------
        // 1. FILTROS DE FECHA ⏳
        // ---------------------------------------------------------
        $fechaInicio = $this->request->getVar('start') ?? date('Y-m-01');
        $fechaFin    = $this->request->getVar('end') ?? date('Y-m-d');
        $sqlFechaFin = $fechaFin . " 23:59:59";

        // IMPORTANTE: El factorTiempo ahora es 1 porque la dispersión 
        // ya creó los registros mensuales en la base de datos.
        $factorTiempo = 1; 

        // Clausula WHERE basada en la fecha de la MÉTRICA (fm), no del prospecto
        $whereFechasFinancieras = " AND fm.created_at >= '$fechaInicio' AND fm.created_at <= '$sqlFechaFin' ";
        // Clausula WHERE basada en prospectos (para el pipeline)
        $whereFechasProspectos = " AND p.created_at >= '$fechaInicio' AND p.created_at <= '$sqlFechaFin' ";

        // ---------------------------------------------------------
        // 2. COMPARATIVA PERIODO ACTUAL VS ANTERIOR (YoY) 📈
        // ---------------------------------------------------------
        
        // A. Periodo ACTUAL (Suma de registros financieros en el rango)
        $sqlActual = "SELECT SUM(fm.total_income_charged) as total 
                      FROM financial_metrics fm
                      JOIN prospects p ON p.id = fm.prospect_id
                      WHERE p.pipeline_stage = 'Cerrado Ganado' 
                      $whereFechasFinancieras";
        
        $montoActual = $db->query($sqlActual)->getRow()->total ?? 0;

        // B. Periodo ANTERIOR (Mismo rango, un año atrás)
        $inicioPrev = date('Y-m-d', strtotime($fechaInicio . ' -1 year'));
        $finPrev    = date('Y-m-d', strtotime($fechaFin . ' -1 year')) . " 23:59:59";
        
        $sqlPrevio = "SELECT SUM(fm.total_income_charged) as total 
                      FROM financial_metrics fm
                      JOIN prospects p ON p.id = fm.prospect_id
                      WHERE p.pipeline_stage = 'Cerrado Ganado' 
                      AND fm.created_at >= '$inicioPrev' AND fm.created_at <= '$finPrev'";
                      
        $montoPrevio = $db->query($sqlPrevio)->getRow()->total ?? 0;

        // C. Variación %
        $diferenciaDinero = $montoActual - $montoPrevio;
        if ($montoPrevio > 0) {
            $porcentajeCrecimiento = (($montoActual - $montoPrevio) / $montoPrevio) * 100;
        } else {
            $porcentajeCrecimiento = ($montoActual > 0) ? 100 : 0;
        }

        // ---------------------------------------------------------
        // 3. VELOCÍMETRO DE META (100 MILLONES) 💰
        // ---------------------------------------------------------
        $metaAnual = 100000000; // 100 Millones
        $ventaPeriodo = $montoActual; 

        $faltaParaMeta = $metaAnual - $ventaPeriodo;
        $porcentajeMeta = ($ventaPeriodo > 0) ? ($ventaPeriodo / $metaAnual) * 100 : 0;
        $porcentajeGrafica = ($porcentajeMeta > 100) ? 100 : $porcentajeMeta;

        // ---------------------------------------------------------
        // 4. GRÁFICAS DE BARRAS (MES A MES) 📊
        // ---------------------------------------------------------
        $anioAnalisis = date('Y', strtotime($fechaFin));
        $anioPasado = $anioAnalisis - 1;

        $clientesActual = array_fill(1, 12, 0);
        $clientesPasado = array_fill(1, 12, 0);
        $ingresoActual  = array_fill(1, 12, 0);
        $ingresoPasado  = array_fill(1, 12, 0);

        // Clientes Ganados (Conteo por fecha de creación del prospecto)
        $sqlClientes = "SELECT YEAR(created_at) as anio, MONTH(created_at) as mes, COUNT(*) as total
                        FROM prospects
                        WHERE pipeline_stage = 'Cerrado Ganado'
                        AND YEAR(created_at) IN ($anioAnalisis, $anioPasado)
                        GROUP BY YEAR(created_at), MONTH(created_at)";
        $resClientes = $db->query($sqlClientes)->getResultArray();
        foreach($resClientes as $row) {
            if($row['anio'] == $anioAnalisis) $clientesActual[$row['mes']] = $row['total'];
            elseif($row['anio'] == $anioPasado) $clientesPasado[$row['mes']] = $row['total'];
        }

        // Ingreso Real Mensual (Suma de registros financieros por su PROPIA fecha)
        $sqlIngreso = "SELECT YEAR(fm.created_at) as anio, MONTH(fm.created_at) as mes, SUM(fm.total_income_charged) as total
                       FROM financial_metrics fm
                       JOIN prospects p ON p.id = fm.prospect_id
                       WHERE p.pipeline_stage = 'Cerrado Ganado'
                       AND YEAR(fm.created_at) IN ($anioAnalisis, $anioPasado)
                       GROUP BY YEAR(fm.created_at), MONTH(fm.created_at)";
        $resIngreso = $db->query($sqlIngreso)->getResultArray();
        foreach($resIngreso as $row) {
            if($row['anio'] == $anioAnalisis) $ingresoActual[$row['mes']] = $row['total'];
            elseif($row['anio'] == $anioPasado) $ingresoPasado[$row['mes']] = $row['total'];
        }

        // ---------------------------------------------------------
        // 5. OTRAS MÉTRICAS
        // ---------------------------------------------------------
        
        // Pipeline Activo (Prospectos no cerrados en el rango de fechas)
        $sqlPipeline = "SELECT SUM(p.ticket_value) as total 
                        FROM prospects p
                        WHERE p.pipeline_stage NOT LIKE '%Cerrado%' $whereFechasProspectos";
        $totalPipeline = ($db->query($sqlPipeline)->getRow()->total ?? 0);

        // Calidad Leads (Grupos de clasificación)
        $sqlGrupos = "SELECT classification_group, COUNT(*) as total 
                      FROM prospects p
                      WHERE 1=1 $whereFechasProspectos
                      GROUP BY classification_group ORDER BY classification_group ASC";
        $gruposData = $db->query($sqlGrupos)->getResultArray();

        // Embudo de Ventas
        $sqlEmbudo = "SELECT pipeline_stage, COUNT(*) as total 
                      FROM prospects p
                      WHERE 1=1 $whereFechasProspectos
                      GROUP BY pipeline_stage 
                      ORDER BY FIELD(pipeline_stage, 'Nuevo', 'Contactado', 'Propuesta', 'Negociacion', 'Cerrado Ganado', 'Cerrado Perdido', 'Cerrado sin decisión')";
        $embudoData = $db->query($sqlEmbudo)->getResultArray();

        // Top Servicios (Por dinero real cobrado en el periodo)
        $sqlServicios = "SELECT s.name, SUM(fm.total_income_charged) as total_dinero
                         FROM financial_metrics fm
                         JOIN prospects p ON p.id = fm.prospect_id
                         JOIN service_types s ON p.service_type_id = s.id
                         WHERE p.pipeline_stage = 'Cerrado Ganado' $whereFechasFinancieras
                         GROUP BY s.name
                         ORDER BY total_dinero DESC LIMIT 5"; 
        $serviciosData = $db->query($sqlServicios)->getResultArray();

        // ---------------------------------------------------------
        // 6. TAREAS Y ACTIVIDADES
        // ---------------------------------------------------------
        $activityModel = new \App\Models\ActivityModel();
        $tareasPendientes = $activityModel->getTareasPendientes(session()->get('id'));

        // ---------------------------------------------------------
        // 7. WIDGET ESPECÍFICO: META 2026 (ENERO VS OBJETIVO)
        // ---------------------------------------------------------
        $ventaHeredada = 55226792.00;
        $metaFinal     = 100000000.00;
        $diferenciaMeta = $metaFinal - $ventaHeredada; 
        $metaMensual   = 5000000.00; // Objetivo de 5M mensuales fijado por el usuario

        // Obtener Ingreso Real de Enero 2026 desde la base de datos
        $sqlEnero = "SELECT SUM(total_income_charged) as total 
                     FROM financial_metrics 
                     WHERE YEAR(created_at) = 2026 AND MONTH(created_at) = 1";
        $ingresoEnero = $db->query($sqlEnero)->getRow()->total ?? 0;

        // Obtener conteo de clientes reales de Enero 2026
        $sqlClientesEnero = "SELECT COUNT(*) as total 
                             FROM prospects 
                             WHERE pipeline_stage = 'Cerrado Ganado' 
                             AND YEAR(created_at) = 2026 AND MONTH(created_at) = 1";
        $clientesEnero = $db->query($sqlClientesEnero)->getRow()->total ?? 0;

        // Trayectoria Ideal
        $trayectoriaMeta = [];
        $acumuladoIdeal = $ventaHeredada;
        for ($i = 1; $i <= 12; $i++) {
            $acumuladoIdeal += $metaMensual;
            $trayectoriaMeta[] = round($acumuladoIdeal, 2);
        }

        // Venta Real Acumulada 2026 (Solo pintamos lo que ya pasó)
        $ventaRealAcumulada = array_fill(0, 12, null);
        $ventaRealAcumulada[0] = round($ventaHeredada + $ingresoEnero, 2);

        // Estatus rápido
        $diferenciaEnero = $ingresoEnero - $metaMensual;
        $estatusEnero = ($ingresoEnero >= $metaMensual) ? 'Meta Alcanzada' : 'Bajo Meta';

        // ---------------------------------------------------------
        // RETORNO A VISTA
        // ---------------------------------------------------------
        return view('dashboard/home', [
            'filtroInicio'          => $fechaInicio,
            'filtroFin'             => $fechaFin,
            'montoActual'           => $montoActual,
            'montoPrevio'           => $montoPrevio,
            'diferenciaDinero'      => $diferenciaDinero,
            'porcentajeCrecimiento' => $porcentajeCrecimiento,
            'ventaTotalAnual'       => $ventaPeriodo,
            'metaAnual'             => $metaAnual,
            'faltaParaMeta'         => $faltaParaMeta,
            'porcentajeMeta'        => $porcentajeMeta,
            'porcentajeGrafica'     => $porcentajeGrafica,
            'anioActual'            => $anioAnalisis,
            'anioPasado'            => $anioPasado,
            'clientesActual'        => array_values($clientesActual),
            'clientesPasado'        => array_values($clientesPasado),
            'ingresoActual'         => array_values($ingresoActual),
            'ingresoPasado'         => array_values($ingresoPasado),
            'totalPipeline'         => $totalPipeline,
            'totalGanado'           => $montoActual,
            'gruposData'            => $gruposData,
            'embudoData'            => $embudoData,
            'serviciosData'         => $serviciosData,
            'tareasPendientes'      => $tareasPendientes,
            'ventaHeredada'         => $ventaHeredada,
            'diferenciaMeta'        => $diferenciaMeta,
            'metaMensual'           => $metaMensual,
            'ingresoEnero'          => $ingresoEnero,
            'clientesEnero'         => $clientesEnero,
            'trayectoriaMeta'       => $trayectoriaMeta,
            'ventaRealAcumulada'    => $ventaRealAcumulada,
            'diferenciaEnero'       => $diferenciaEnero,
            'estatusEnero'          => $estatusEnero
        ]);
    }
}