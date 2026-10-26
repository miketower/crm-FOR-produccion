<?php

namespace App\Controllers;

class Importar extends BaseController 
{
    public function index() 
    {
        // Si no usas $this->viewData, puedes pasar un array vacío []
        $data = isset($this->viewData) ? $this->viewData : [];
        return view('importar/subir_csv', $data); 
    }

    // 🚨 SOLUCIÓN: Agregamos el método que le hace falta al controlador para renderizar la vista
    public function ventas()
    {
        // Forzamos los datos limpios de la sesión por si hay mensajes de error o éxito
        $data = isset($this->viewData) ? $this->viewData : [];
        
        // Renderizamos de forma limpia la vista que creamos para Sam
        return view('importar/subir_ventas', $data);
    }

    public function procesar()
    {
        $file = $this->request->getFile('csv_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Archivo inválido o corrupto.');
        }

        $db = \Config\Database::connect();

        // 1. WIPE DATA (Limpieza profunda si se seleccionó)
        if ($this->request->getPost('wipe_data') == '1') {
            // Desactivamos temporalmente las llaves foráneas para poder vaciar
            $db->query('SET FOREIGN_KEY_CHECKS = 0');
            
            // Usamos SQL puro en lugar del Query Builder para evitar bugs del framework
            $db->query('TRUNCATE TABLE analysis_qualitative');
            $db->query('TRUNCATE TABLE decision_profile');
            $db->query('TRUNCATE TABLE financial_metrics');
            $db->query('TRUNCATE TABLE prospects');
            
            // Volvemos a activar las llaves foráneas
            $db->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        // 2. ABRIR EL CSV
        $filePath = $file->getTempName();
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            
            // Saltamos la primera fila (los encabezados)
            fgetcsv($handle, 10000, ",");
            
            $count = 0;
            $db = \Config\Database::connect(); // Aseguramos la conexión
        
            // 3. RECORRER CADA FILA DEL CSV
            while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
                
                // Si la fila está vacía o no tiene al menos la empresa, saltamos
                if (empty(trim($row[1]))) continue;
        
                // --- MAPEO DE COLUMNAS SEGÚN TU ARCHIVO ---
                $fechaFila      = $this->convertirFechaInteligente(isset($row[0]) ? $row[0] : null);
                $empresa        = $this->cleanString(isset($row[1]) ? $row[1] : '');
                $giro           = 1; // Hardcoded temporal
                $tipoServicio   = $this->cleanString(isset($row[3]) ? $row[3] : '');
                
                // --- EL BLINDAJE PARA LOS EMPLEADOS ---
                $empleadosRaw   = isset($row[4]) ? trim((string)$row[4]) : '';
                $empleados      = ($empleadosRaw === '') ? 0 : (int)$empleadosRaw;
                
                $estadoRaw      = isset($row[5]) ? $row[5] : '';
                $estadoId       = $this->getStateIdByName($estadoRaw, $db);
                
                // Limpieza de Dinero
                $valorTicket    = $this->limpiarDineroRobust(isset($row[11]) ? $row[11] : 0);
                $ingresoCobrado = $this->limpiarDineroRobust(isset($row[12]) ? $row[12] : 0);
        
                // Lógica de Estatus
                $estatus = $this->cleanString(isset($row[10]) ? $row[10] : '');
                if (empty($estatus) && $ingresoCobrado > 0) {
                    $estatus = 'Cerrado Ganado';
                } elseif (empty($estatus)) {
                    $estatus = 'Nuevo'; 
                }
        
                // --- INSERCIÓN EN PROSPECTS ---
                $dataProspect = [
                    'company_name'    => $empresa,
                    'pipeline_stage'  => $estatus,
                    'ticket_value'    => $valorTicket,
                    'created_at'      => $fechaFila,
                    'updated_at'      => $fechaFila,
                    'created_by'      => 1  
                ];
                
                $db->table('prospects')->insert($dataProspect);
                $prospectId = $db->insertID();
        
                // --- LÓGICA DE DISPERSIÓN MENSUAL (Solo para prospectos Ganados) ---
                if ($estatus === 'Cerrado Ganado' && $ingresoCobrado > 0) {
                    
                    $fechaInicio = new \DateTime($fechaFila);
                    $fechaHoy    = new \DateTime(); // Hasta el día de hoy
                    $fechaHoy->modify('first day of this month'); // Ajuste para incluir el mes actual completo
        
                    // Si la fecha de creación es futura por error, solo insertamos una vez
                    if ($fechaInicio > $fechaHoy) {
                        $db->table('financial_metrics')->insert([
                            'prospect_id'          => $prospectId,
                            'total_income_charged' => $ingresoCobrado,
                            'created_at'           => $fechaFila
                        ]);
                    } else {
                        // Generamos un registro por cada mes desde la creación hasta hoy
                        $intervalo = new \DateInterval('P1M');
                        $periodo   = new \DatePeriod($fechaInicio, $intervalo, $fechaHoy->modify('+1 month'));
        
                        foreach ($periodo as $dt) {
                            $db->table('financial_metrics')->insert([
                                'prospect_id'          => $prospectId,
                                'total_income_charged' => $ingresoCobrado, // Se repite el cobro mensual
                                'created_at'           => $dt->format('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
        
                $count++;
            }
            fclose($handle);
            
            return redirect()->to('/importar')->with('success', "¡Éxito! $count registros procesados con dispersión histórica mensual.");
        }

        return redirect()->back()->with('error', 'No se pudo leer el archivo CSV.');
    }

    // =========================================================================
    // FUNCIONES BLINDADAS PARA LIMPIEZA DE DATOS
    // =========================================================================

    private function convertirFechaInteligente($fechaRaw)
    {
        // 1. Limpiar espacios
        $fechaRaw = trim($fechaRaw);
        
        // 2. Si ya viene en formato YYYY-MM-DD, devolver tal cual
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaRaw)) {
            return $fechaRaw;
        }

        // 3. Intentar convertir desde formato DD/MM/YYYY
        $date = \DateTime::createFromFormat('d/m/Y', $fechaRaw);
        if ($date) {
            return $date->format('Y-m-d');
        }

        // 4. Si todo falla, devolver NULL para que no inserte 0000-00-00
        return null; 
    }

    private function limpiarDineroRobust($val) 
    {
        if (empty($val)) return 0.00;
        // Quitamos comas, signos de dólar, espacios y comillas
        $val = str_replace([',', '$', ' ', '"', "'"], '', $val);
        return is_numeric($val) ? (float)$val : 0.00;
    }

    private function cleanString($str): string 
    {
        // Al poner (string), forzamos a que si viene un null, se convierta en "" (texto vacío)
        return trim(str_replace('"', '', (string)$str));
    }

    // Le quitamos la palabra "string" antes de la variable para que acepte de todo
    private function getOrCreateServiceId($nombreServicio, $db)
    {
        $nombreServicio = trim((string)$nombreServicio);
        
        // Si después de limpiarlo resulta que estaba vacío, regresamos null para que la BD guarde el campo en blanco sin problemas
        if (empty($nombreServicio)) return null;

        $row = $db->table('service_types')->where('name', $nombreServicio)->get()->getRow();
        if ($row) {
            return $row->id;
        }

        $db->table('service_types')->insert(['name' => $nombreServicio]);
        return $db->insertID();
    }

    private function getStateIdByName($nombreEstado, $db)
    {
        $nombreEstado = trim((string)$nombreEstado);
        if (empty($nombreEstado)) return null;

        // Usamos LIKE para tolerar diferencias (ej. "NUEVO LEON" vs "Nuevo León")
        $row = $db->table('states')->like('name', $nombreEstado, 'both')->get()->getRow();
        
        if ($row) {
            return $row->id;
        }

        return null; // Devuelve nulo si no encuentra coincidencia
    }

    public function procesarVentasCsv()
    {
        $file        = $this->request->getFile('csv_file');
        $tipoVentaId = $this->request->getPost('cat_tipo_venta_id'); // ID del Canal (1, 2, 3)
        
        // 🚨 CAPTURAMOS EL PERIODO DESDE LA PANTALLA (Ejemplo: "2026-03")
        $periodoInput = $this->request->getPost('periodo_retroactivo'); 

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Archivo inválido o corrupto.');
        }

        if (empty($tipoVentaId) || empty($periodoInput)) {
            return redirect()->back()->with('error', 'Por favor, selecciona el canal comercial y el mes correspondiente.');
        }

        $filePath = $file->getTempName();
        
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            
            fgetcsv($handle, 10000, ","); // Saltamos encabezados
            $count = 0;
            $db = \Config\Database::connect();

            // 🚨 NUEVA LÓGICA DE BORRADO AUTOMÁTICO 🚨
            // Borramos las ventas de ese mes exacto y de ese canal específico 
            // para evitar duplicados antes de insertar la nueva sábana de datos.
            $db->table('ventas_flujos')
            ->where('periodo', $periodoInput)
            ->where('cat_tipo_venta_id', (int)$tipoVentaId)
            ->delete();
            
            // Formateamos la fecha base para las columnas SQL (Ejemplo: "2026-03-01")
            $fechaAsignada = $periodoInput . "-01"; 

            while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
                
                if (!isset($row[2]) || !isset($row[4]) || trim((string)$row[2]) === '' || trim((string)$row[4]) === '') {
                    continue; 
                }

                // Filtro anti-totales
                $contenidoFila = strtolower(trim($row[2]) . ' ' . trim($row[3]) . ' ' . trim($row[4]));
                if (str_contains($contenidoFila, 'total') || str_contains($contenidoFila, 'totales') || str_contains($contenidoFila, 'suma')) {
                    continue;
                }

                $clienteRaw  = trim($row[4]);
                $razonSocial = preg_replace('/^\d+\s*-\s*/', '', $clienteRaw);
                $razonSocial = $this->cleanString($razonSocial);

                $montoVendido = $this->limpiarDineroRobust($row[8] ?? 0);   
                $montoCobrado = $this->limpiarDineroRobust($row[9] ?? 0);   
                $comisionReal = $this->limpiarDineroRobust($row[11] ?? 0);  

                // 1. Relación de Clientes
                $cliente = $db->table('clientes')->where('razon_social', $razonSocial)->get()->getRow();
                if ($cliente) {
                    $clienteId = $cliente->id;
                    $socioId   = $cliente->socio_id;
                } else {
                    $db->table('clientes')->insert([
                        'razon_social' => $razonSocial,
                        'socio_id'     => null,
                        'estatus'      => 1,
                        'created_at'   => date('Y-m-d H:i:s')
                    ]);
                    $clienteId = $db->insertID();
                    $socioId   = null;
                }

                // 2. Inyección indexada con la fecha retroactiva correcta de Sam 📅
                $dataVenta = [
                    'cliente_id'              => $clienteId,
                    'cat_esquema_servicio_id' => 1, 
                    'cat_tipo_venta_id'       => (int)$tipoVentaId, 
                    'monto_vendido'           => $montoVendido,
                    'monto_cobrado'           => $montoCobrado,
                    'comision_bruta'          => $comisionReal, 
                    'fecha_vendido'           => $fechaAsignada, // 👈 Bloqueado al mes real del Excel
                    'fecha_cobrado'           => $montoCobrado > 0 ? $fechaAsignada : null,
                    'periodo'                 => $periodoInput,  // 👈 Ej: "2026-03"
                    'estatus'                 => 1,
                    'created_at'              => date('Y-m-d H:i:s')
                ];

                $db->table('ventas_flujos')->insert($dataVenta);
                $ventaFlujoId = $db->insertID();

                // 3. Automatización de la Pre-Dispersión a Socios
                if (!is_null($socioId) && $comisionReal > 0) {
                    $db->table('dispersiones_comisiones')->insert([
                        'venta_flujo_id'            => $ventaFlujoId,
                        'socio_id'                  => $socioId,
                        'cat_esquema_dispersion_id' => 1, 
                        'monto_participacion'       => $comisionReal, 
                        'created_at'                => date('Y-m-d H:i:s')
                    ]);
                }

                $count++;
            }
            
            fclose($handle);
            return redirect()->to('importar/ventas')->with('success', "¡Búnker Sincronizado! Se procesaron $count registros asignados correctamente al periodo $periodoInput.");
        }
        return redirect()->back()->with('error', 'No se pudo leer la estructura del archivo.');
    }

    public function procesarUnificado()
    {
        // 1. Obtener archivos y datos del formulario
        $file = $this->request->getFile('csv_file');
        $periodo = $this->request->getPost('periodo'); // Ejemplo: '2026-06'
        $wipeData = $this->request->getPost('wipe_data'); // Checkbox
        $catIdSeleccionado = $this->request->getPost('cat_tipo_venta_id'); // NUEVO: Categoría seleccionada

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Archivo inválido o no seleccionado.');
        }
        
        // Validación de seguridad para la categoría
        if (empty($catIdSeleccionado)) {
            return redirect()->back()->with('error', 'Debes seleccionar una categoría de tipo de venta.');
        }

        $db = \Config\Database::connect();

        // 2. Lógica de Borrado Inteligente
        if ($wipeData && !empty($periodo)) {
            $db->table('ventas_flujos')
            ->where('periodo', $periodo)
            ->delete();
        }

        // 3. Procesar el CSV
        $handle = fopen($file->getTempName(), "r");
        fgetcsv($handle); // Saltamos la línea de encabezados

        while (($row = fgetcsv($handle)) !== FALSE) {
            // A. Normalización de Servicio
            $nombreServicio = trim($row[3]);
            $servicio = $db->table('service_types')
                            ->where('name', $nombreServicio)
                            ->get()
                            ->getRow();
            $serviceTypeId = $servicio ? $servicio->id : 1; 

            // B. Identificar o Crear Prospecto
            // 1. Extraemos el nombre original del CSV
            $nombreCsv = trim($row[1]);
            
            // 2. MAGIA: Rasuramos el prefijo (3 números + espacio + guion + espacio)
            $nombreEmpresa = preg_replace('/^[0-9]{3} - /', '', $nombreCsv);
            $nombreEmpresa = trim($nombreEmpresa);

            // 3. Buscamos al prospecto con el nombre ya homologado
            $prospecto = $db->table('prospects')
                            ->where('company_name', $nombreEmpresa)
                            ->get()
                            ->getRow();
            $userId = session()->has('user_id') ? session()->get('user_id') : 1;

            if (!$prospecto) {
                $db->table('prospects')->insert([
                    'company_name'         => $nombreEmpresa,
                    'service_type_id'      => $serviceTypeId,
                    'pipeline_stage'       => $row[9] ?? 'Nuevo',
                    'classification_group' => $row[10] ?? 'D',
                    'created_by'           => $userId,
                    'created_at'           => date('Y-m-d H:i:s')
                ]);
                $prospectId = $db->insertID();
            } else {
                $prospectId = $prospecto->id;
            }

            // C. Insertar Venta asociada
            $nombreEsquema = trim($row[3]); 
            $esquema = $db->table('cat_esquemas_servicio')
                        ->like('nombre', $nombreEsquema) 
                        ->get()
                        ->getRow();

            $catEsquemaId = $esquema ? $esquema->id : 1;

            // D. Insertar en ventas_flujos con la categoría capturada del formulario
            $db->table('ventas_flujos')->insert([
                'prospect_id'                => $prospectId,
                'ingreso_bruto_total'        => $this->limpiarDineroRobust($row[6]), // Antes monto_vendido
                'total_validado_facturacion' => $this->limpiarDineroRobust($row[7]), // Antes monto_cobrado
                'comision_bruta'             => $this->limpiarDineroRobust($row[8]),
                'cat_esquema_servicio_id'    => $catEsquemaId,
                'cat_tipo_venta_id'          => $catIdSeleccionado,
                'periodo'                    => $periodo,
                'fecha_vendido'              => $this->convertirFechaInteligente($row[0]),
                'created_at'                 => date('Y-m-d H:i:s')
            ]);
        }
        
        fclose($handle);
        return redirect()->to('/importar')->with('success', '¡Carga Maestra completada correctamente con categoría ID: ' . $catIdSeleccionado . '!');
    }
}