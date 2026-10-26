<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Cotizador extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        // Protección de ruta idéntica a Prospectos
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

        $fechaInicio = $request->getGet('fecha_inicio');
        $fechaFin = $request->getGet('fecha_fin');

        $builder = $db->table('propuestas_for');

        // 2. Filtro: Búsqueda por Texto (Cliente o Solicitante)
        if ($search = $request->getGet('search')) {
            $builder->groupStart()
                    ->like('nombre_cliente', $search)
                    ->orLike('solicitante', $search)
                    ->groupEnd();
        }

        // 3. Filtro: Tipo de Plan
        if ($plan = $request->getGet('plan_filtro')) {
            $builder->where('plan_requerido', $plan);
        }

        // 4. Filtro: Nivel de Riesgo (Puede ser 0, por eso validamos que no sea null o cadena vacía)
        if ($riesgo = $request->getGet('riesgo_filtro')) {
            if ($riesgo !== '') {
                $builder->where('nivel_riesgo', $riesgo);
            }
        }

        // 5. Filtro por Fechas
        if (!empty($fechaInicio)) {
            $builder->where('DATE(fecha_creacion) >=', $fechaInicio);
        }
        if (!empty($fechaFin)) {
            $builder->where('DATE(fecha_creacion) <=', $fechaFin);
        }

        $builder->orderBy('fecha_creacion', 'DESC');

        // 6. Paginación (15 registros por página, igual que en Prospectos)
        $perPage = 15;
        $total = $builder->countAllResults(false);
        $page = (int)($request->getGet('page') ?? 1);
        $propuestas = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        return view('cotizador/listado', array_merge($this->viewData ?? [], [
            'propuestas'   => $propuestas,
            'pager'        => $pager->makeLinks($page, $perPage, $total, 'bootstrap_full'),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin
        ]));
    }

    // Este es el método que ahora cargará el formulario que hicimos en el paso anterior
    public function nuevo()
    {
        return view('cotizador/nuevo', $this->viewData ?? []);
    }

    public function guardar()
    {
        $db = \Config\Database::connect();
        
        // Recibir payload JSON de custom.js
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se recibieron datos.']);
        }

        try {
            $db->transStart(); // Iniciar transacción segura

            // 1. Insertar en propuestas_for
            $nombreCliente = !empty($data['nombre_cliente']) ? $data['nombre_cliente'] : 'Cliente Genérico';
            
            $dataPropuesta = [
                'nombre_cliente'        => $nombreCliente,
                'diag_360_inicial'      => $data['diag_360_inicial'] ?? 0,
                'fecha_propuesta'       => $data['fecha_propuesta'] ?? '',
                'solicitante'           => $data['solicitante'] ?? '',
                'cargo_solicitante'     => $data['cargo_solicitante'] ?? '',
                'impresion_diagnostica' => $data['impresion_diagnostica'] ?? '',
                'req_intervencion'      => $data['req_intervencion'] ?? '',
                'motivo_intervencion'   => $data['motivo_intervencion'] ?? '',
                'nivel_riesgo'          => $data['nivel_riesgo'] ?? 0,
                'plan_requerido'        => $data['plan_integral'] ?? '',
                'detalle_otros'         => $data['otro_plan_detalle'] ?? '',
                'comision_pct'          => $data['comision_porcentaje'] ?? 0,
                'periodo_pago'          => $data['periodo_pago'] ?? '',
                
                // Agregamos el Escenario Actual a la BD
                'actual_bruto'          => $data['actual_bruto'] ?? 0,
                'actual_nomina'         => $data['actual_nomina'] ?? 0,
                'actual_excedente'      => $data['actual_excedente'] ?? 0,
                'actual_carga'          => $data['actual_carga'] ?? 0,
                'actual_comision'       => $data['actual_comision'] ?? 0,
                'actual_total'          => $data['actual_total'] ?? 0
            ];
            
            $db->table('propuestas_for')->insert($dataPropuesta);
            $propuesta_id = $db->insertID();

            // 2. Guardar Market Benchmarks
            if (!empty($data['benchmarks'])) {
                foreach ($data['benchmarks'] as $bench) {
                    $db->table('market_benchmark')->insert([
                        'propuesta_id'  => $propuesta_id,
                        'puesto'        => $bench['puesto'],
                        'percentile_25' => $bench['p25'],
                        'mean'          => $bench['mean'],
                        'median'        => $bench['median'],
                        'percentile_75' => $bench['p75']
                    ]);
                }
            }

            // 3. Guardar Proem
            if ($data['plan_integral'] === 'Proem' || $data['plan_integral'] === 'Mixto') {
                $db->table('detalle_proem')->insert([
                    'propuesta_id'            => $propuesta_id,
                    'base_100'                => $data['base_100'] ?? 0,
                    'neto_100'                => $data['neto_100'] ?? 0,
                    'carga_100'               => $data['carga_100'] ?? 0,
                    // Nuevos campos 100% IMSS
                    'comision_100'            => $data['comision_100'] ?? 0,
                    'comision_100_afecta'     => $data['comision_100_afecta'] ?? 0,
                    
                    'bruto_proem'             => $data['bruto_proem'] ?? 0,
                    'neto_proem'              => $data['neto_proem'] ?? 0,
                    'carga_proem'             => $data['carga_proem'] ?? 0,
                    
                    // Nuevo campo PROEM (Empleado)
                    'comision_empleado_proem' => $data['comision_empleado_proem'] ?? 0,
                    
                    'costo_op_100'            => $data['total_100'] ?? 0,
                    'excedente'               => $data['excedente'] ?? 0,
                    'comision_monto'          => $data['comision_for_monto'] ?? 0, // Ganancia FOR
                    'total_proem'             => $data['total_proem'] ?? 0,
                    'ahorro_mensual'          => $data['ahorro_mensual'] ?? 0
                ]);

                if (!empty($data['perfiles_proem'])) {
                    foreach ($data['perfiles_proem'] as $perfil) {
                        if (!empty($perfil['cantidad']) && !empty($perfil['cargo'])) {
                            $db->table('perfiles_plantilla')->insert([
                                'propuesta_id' => $propuesta_id,
                                'tipo_plan'    => 'proem',
                                'cantidad'     => $perfil['cantidad'],
                                'cargo'        => $perfil['cargo']
                            ]);
                        }
                    }
                }
            }

            // 4. Guardar Especializado
            if ($data['plan_integral'] === 'Servicio Especializado' || $data['plan_integral'] === 'Mixto') {
                $db->table('detalle_especializado')->insert([
                    'propuesta_id'   => $propuesta_id,
                    'base_100'       => $data['esp_base_100'] ?? 0,
                    'neto_100'       => $data['esp_neto_100'] ?? 0,
                    'carga_100'      => $data['esp_carga_100'] ?? 0,
                    'sueldos_for'    => $data['esp_bruto_for'] ?? 0,
                    'neto_for'       => $data['esp_neto_for'] ?? 0,
                    'excedente'      => $data['esp_excedente'] ?? 0,
                    'carga_for'      => $data['esp_carga_for'] ?? 0,
                    'costo_op_100'   => $data['esp_total_100'] ?? 0,
                    'comision_monto' => $data['esp_comision_monto'] ?? 0,
                    'total_for'      => $data['esp_total_for'] ?? 0,
                    'diferencia'     => $data['esp_diferencia'] ?? 0
                ]);

                if (!empty($data['perfiles_esp'])) {
                    foreach ($data['perfiles_esp'] as $perfil) {
                        if (!empty($perfil['cantidad']) && !empty($perfil['cargo'])) {
                            $db->table('perfiles_plantilla')->insert([
                                'propuesta_id' => $propuesta_id,
                                'tipo_plan'    => 'esp',
                                'cantidad'     => $perfil['cantidad'],
                                'cargo'        => $perfil['cargo']
                            ]);
                        }
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar en la base de datos.']);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Propuesta guardada correctamente', 'id' => $propuesta_id]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Excepción: ' . $e->getMessage()]);
        }
    }

    public function subir_pdf()
    {
        $file = $this->request->getFile('pdf');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'Propuesta_FOR_' . date('Ymd_His') . '.pdf';
            // Guardamos en la carpeta public/uploads/propuestas
            $file->move(FCPATH . 'uploads/propuestas', $newName);
            
            $urlCompleta = base_url('uploads/propuestas/' . $newName);
            return $this->response->setJSON(['success' => true, 'url' => $urlCompleta]);
        }

        return $this->response->setJSON(['success' => false, 'error' => 'No se pudo guardar el archivo.']);
    }

    public function ver($id)
    {
        $db = \Config\Database::connect();
        
        // 1. Traer la propuesta principal
        $propuesta = $db->table('propuestas_for')->where('id', $id)->get()->getRowArray();
        
        if (!$propuesta) {
            return redirect()->to('/cotizador')->with('error', 'Cotización no encontrada');
        }

        // 2. Traer la información de las tablas hijas
        $proem = $db->table('detalle_proem')->where('propuesta_id', $id)->get()->getRowArray();
        $esp = $db->table('detalle_especializado')->where('propuesta_id', $id)->get()->getRowArray();
        $benchmarks = $db->table('market_benchmark')->where('propuesta_id', $id)->get()->getResultArray();
        $perfiles = $db->table('perfiles_plantilla')->where('propuesta_id', $id)->get()->getResultArray();

        return view('cotizador/ver', array_merge($this->viewData ?? [], [
            'propuesta'  => $propuesta,
            'proem'      => $proem,
            'esp'        => $esp,
            'benchmarks' => $benchmarks,
            'perfiles'   => $perfiles
        ]));
    }

    public function editar($id)
    {
        $db = \Config\Database::connect();
        
        $propuesta = $db->table('propuestas_for')->where('id', $id)->get()->getRowArray();
        
        if (!$propuesta) {
            return redirect()->to('/cotizador')->with('error', 'Cotización no encontrada');
        }

        $proem = $db->table('detalle_proem')->where('propuesta_id', $id)->get()->getRowArray();
        $esp = $db->table('detalle_especializado')->where('propuesta_id', $id)->get()->getRowArray();
        $benchmarks = $db->table('market_benchmark')->where('propuesta_id', $id)->get()->getResultArray();
        $perfiles = $db->table('perfiles_plantilla')->where('propuesta_id', $id)->get()->getResultArray();

        return view('cotizador/editar', array_merge($this->viewData ?? [], [
            'propuesta'  => $propuesta,
            'proem'      => $proem,
            'esp'        => $esp,
            'benchmarks' => $benchmarks,
            'perfiles'   => $perfiles
        ]));
    }

    public function actualizar($id)
    {
        $db = \Config\Database::connect();
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se recibieron datos.']);
        }

        try {
            $db->transStart();

            // 1. Actualizar tabla principal
            $dataPropuesta = [
                'nombre_cliente'        => $data['nombre_cliente'] ?? 'Cliente Genérico',
                'diag_360_inicial'      => $data['diag_360_inicial'] ?? 0,
                'fecha_propuesta'       => $data['fecha_propuesta'] ?? '',
                'solicitante'           => $data['solicitante'] ?? '',
                'cargo_solicitante'     => $data['cargo_solicitante'] ?? '',
                'impresion_diagnostica' => $data['impresion_diagnostica'] ?? '',
                'req_intervencion'      => $data['req_intervencion'] ?? '',
                'motivo_intervencion'   => $data['motivo_intervencion'] ?? '',
                'nivel_riesgo'          => $data['nivel_riesgo'] ?? 0,
                'plan_requerido'        => $data['plan_integral'] ?? '',
                'detalle_otros'         => $data['otro_plan_detalle'] ?? '',
                'comision_pct'          => $data['comision_porcentaje'] ?? 0,
                'periodo_pago'          => $data['periodo_pago'] ?? '',
                // Escenario Actual
                'actual_bruto'          => $data['actual_bruto'] ?? 0,
                'actual_nomina'         => $data['actual_nomina'] ?? 0,
                'actual_excedente'      => $data['actual_excedente'] ?? 0,
                'actual_carga'          => $data['actual_carga'] ?? 0,
                'actual_comision'       => $data['actual_comision'] ?? 0,
                'actual_total'          => $data['actual_total'] ?? 0
            ];
            $db->table('propuestas_for')->where('id', $id)->update($dataPropuesta);

            // 2. Limpiar y recrear Benchmarks (es más seguro borrar e insertar)
            $db->table('market_benchmark')->where('propuesta_id', $id)->delete();
            if (!empty($data['benchmarks'])) {
                foreach ($data['benchmarks'] as $bench) {
                    $db->table('market_benchmark')->insert([
                        'propuesta_id'  => $id,
                        'puesto'        => $bench['puesto'],
                        'percentile_25' => $bench['p25'],
                        'mean'          => $bench['mean'],
                        'median'        => $bench['median'],
                        'percentile_75' => $bench['p75']
                    ]);
                }
            }

            // 3. Limpiar y recrear Perfiles
            $db->table('perfiles_plantilla')->where('propuesta_id', $id)->delete();
            
            // 4. Actualizar Proem (Si aplica)
            if ($data['plan_integral'] === 'Proem' || $data['plan_integral'] === 'Mixto') {
                $existeProem = $db->table('detalle_proem')->where('propuesta_id', $id)->countAllResults();
                $dataProem = [
                    'propuesta_id'            => $id,
                    'base_100'                => $data['base_100'] ?? 0,
                    'neto_100'                => $data['neto_100'] ?? 0,
                    'carga_100'               => $data['carga_100'] ?? 0,
                    // Nuevos campos 100% IMSS
                    'comision_100'            => $data['comision_100'] ?? 0,
                    'comision_100_afecta'     => $data['comision_100_afecta'] ?? 0,
                    
                    'bruto_proem'             => $data['bruto_proem'] ?? 0,
                    'neto_proem'              => $data['neto_proem'] ?? 0,
                    'carga_proem'             => $data['carga_proem'] ?? 0,
                    
                    // Nuevo campo PROEM (Empleado)
                    'comision_empleado_proem' => $data['comision_empleado_proem'] ?? 0,

                    'costo_op_100'            => $data['total_100'] ?? 0,
                    'excedente'               => $data['excedente'] ?? 0,
                    'comision_monto'          => $data['comision_for_monto'] ?? 0, // Ganancia FOR
                    'total_proem'             => $data['total_proem'] ?? 0,
                    'ahorro_mensual'          => $data['ahorro_mensual'] ?? 0
                ];
                if ($existeProem > 0) {
                    $db->table('detalle_proem')->where('propuesta_id', $id)->update($dataProem);
                } else {
                    $db->table('detalle_proem')->insert($dataProem);
                }

                if (!empty($data['perfiles_proem'])) {
                    foreach ($data['perfiles_proem'] as $perfil) {
                        $db->table('perfiles_plantilla')->insert([
                            'propuesta_id' => $id, 'tipo_plan' => 'proem',
                            'cantidad' => $perfil['cantidad'], 'cargo' => $perfil['cargo']
                        ]);
                    }
                }
            }

            // 5. Actualizar Especializado (Si aplica)
            if ($data['plan_integral'] === 'Servicio Especializado' || $data['plan_integral'] === 'Mixto') {
                $existeEsp = $db->table('detalle_especializado')->where('propuesta_id', $id)->countAllResults();
                $dataEsp = [
                    'propuesta_id'   => $id, // O $propuesta_id en guardar()
                    'base_100'       => $data['esp_base_100'] ?? 0,
                    'neto_100'       => $data['esp_neto_100'] ?? 0,
                    'carga_100'      => $data['esp_carga_100'] ?? 0,
                    
                    'sueldos_for'    => $data['esp_bruto_for'] ?? 0, // Bruto FOR
                    'neto_for'       => $data['esp_neto_for'] ?? 0,  // Nuevo
                    'excedente'      => $data['esp_excedente'] ?? 0, // Nuevo
                    
                    'carga_for'      => $data['esp_carga_for'] ?? 0,
                    'costo_op_100'   => $data['esp_total_100'] ?? 0,
                    'comision_monto' => $data['esp_comision_monto'] ?? 0,
                    'total_for'      => $data['esp_total_for'] ?? 0,
                    'diferencia'     => $data['esp_diferencia'] ?? 0
                ];
                if ($existeEsp > 0) {
                    $db->table('detalle_especializado')->where('propuesta_id', $id)->update($dataEsp);
                } else {
                    $db->table('detalle_especializado')->insert($dataEsp);
                }

                if (!empty($data['perfiles_esp'])) {
                    foreach ($data['perfiles_esp'] as $perfil) {
                        $db->table('perfiles_plantilla')->insert([
                            'propuesta_id' => $id, 'tipo_plan' => 'esp',
                            'cantidad' => $perfil['cantidad'], 'cargo' => $perfil['cargo']
                        ]);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al actualizar en la base de datos.']);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Cotización actualizada correctamente']);

        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Excepción: ' . $e->getMessage()]);
        }
    }
}
