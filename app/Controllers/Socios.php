<?php

namespace App\Controllers;

class Socios extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $model = new \CodeIgniter\Model();
        $model->setTable('socios');

        // Configurar la consulta completa con JOINs directamente en el Modelo
        $model->select('socios.*, cat.nombre as semaforo, cat.color_hex, r.nombre as red_nombre')
              ->join('cat_semaforos_socio cat', 'cat.id = socios.cat_semaforo_id', 'left')
              ->join('socios r', 'r.id = socios.red_id', 'left');

        // Aplicar filtros de búsqueda
        if ($buscar = $this->request->getGet('buscar')) {
            $model->like('socios.nombre', $buscar);
        }
        if ($semaforoId = $this->request->getGet('semaforo')) {
            $model->where('socios.cat_semaforo_id', $semaforoId);
        }

        $model->orderBy('socios.nombre', 'ASC');

        $data = array_merge($this->viewData ?? [], [
            'socios'           => $model->paginate(10),
            'pager'            => $model->pager,
            'semaforos'        => $db->table('cat_semaforos_socio')->get()->getResultArray(),
            'todos_los_socios' => $db->table('socios')->orderBy('nombre', 'ASC')->get()->getResultArray()
        ]);

        return view('socios/index', $data);
    }

    public function guardar()
    {
        $db = \Config\Database::connect();
        
        $data = [
            'nombre'                => $this->request->getPost('nombre'),
            'celular'               => $this->request->getPost('celular'),
            'red_id'                => $this->request->getPost('red_id') ?: null,
            'cat_semaforo_id'       => $this->request->getPost('cat_semaforo_id') ?: null,
            'estatus_convenio'      => $this->request->getPost('estatus_convenio'),
            'responsable_resguardo' => $this->request->getPost('responsable_resguardo'),
            'notas'                 => $this->request->getPost('notas'),
            'estatus'               => 'Activo',
            'created_at'            => date('Y-m-d H:i:s'),
            'nivel_participacion'   => $this->request->getPost('nivel_participacion'),
            'servicios_comisiona'   => $this->request->getPost('servicios_comisiona'),
            'tipo_participacion'    => $this->request->getPost('tipo_participacion'),
            'fecha_entrada'         => $this->request->getPost('fecha_entrada') ?: null,
        ];

        $db->table('socios')->insert($data);

        return redirect()->to('/socios')->with('msg', 'Asociado FOR registrado correctamente.');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();
        
        $socio = $db->table('socios s')
            ->select('s.*, cat.nombre as semaforo, cat.color_hex, r.nombre as red_nombre')
            ->join('cat_semaforos_socio cat', 'cat.id = s.cat_semaforo_id', 'left')
            ->join('socios r', 'r.id = s.red_id', 'left')
            ->where('s.id', $id)
            ->get()->getRowArray();

        if (!$socio) {
            return redirect()->to('/socios')->with('msg', 'Asociado FOR no encontrado.');
        }

        $invitados = $db->table('socios s')
            ->select('s.id, s.nombre, cat.nombre as semaforo, cat.color_hex')
            ->join('cat_semaforos_socio cat', 'cat.id = s.cat_semaforo_id', 'left')
            ->where('s.red_id', $id)
            ->get()->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'socio'     => $socio,
            'invitados' => $invitados
        ]);

        return view('socios/detalle', $data);
    }

    public function editar($id)
    {
        $db = \Config\Database::connect();
        
        $data['socio'] = $db->table('socios')->where('id', $id)->get()->getRowArray();
        $data['semaforos'] = $db->table('cat_semaforos_socio')->get()->getResultArray();
        $data['posibles_redes'] = $db->table('socios')->where('id !=', $id)->orderBy('nombre', 'ASC')->get()->getResultArray();

        return view('socios/editar', array_merge($this->viewData ?? [], $data));
    }

    public function actualizar($id)
    {
        $db = \Config\Database::connect();
        
        $data = [
            'nombre'                => $this->request->getPost('nombre'),
            'celular'               => $this->request->getPost('celular'),
            'red_id'                => $this->request->getPost('red_id') ?: null,
            'cat_semaforo_id'       => $this->request->getPost('cat_semaforo_id') ?: null,
            'estatus'               => $this->request->getPost('estatus'),
            'estatus_convenio'      => $this->request->getPost('estatus_convenio'),
            'fecha_firma'           => $this->request->getPost('fecha_firma') ?: null,
            'ubicacion_fisica'      => $this->request->getPost('ubicacion_fisica'),
            'ubicacion_digital'     => $this->request->getPost('ubicacion_digital'),
            'responsable_resguardo' => $this->request->getPost('responsable_resguardo'),
            'notas'                 => $this->request->getPost('notas'),
            'updated_at'            => date('Y-m-d H:i:s'),
            'nivel_participacion'   => $this->request->getPost('nivel_participacion'), // <-- HABILITADO
            'servicios_comisiona'   => $this->request->getPost('servicios_comisiona'),
            'tipo_participacion'    => $this->request->getPost('tipo_participacion'),
            'fecha_entrada'         => $this->request->getPost('fecha_entrada') ?: null,
        ];

        $db->table('socios')->where('id', $id)->update($data);

        return redirect()->to('/socios/show/' . $id)->with('msg', 'Asociado FOR/Convenio actualizado correctamente.');
    }

/*************  ✨ Windsurf Command ⭐  *************/
/**
 * Muestra el cierre del mes seleccionado.
 *
 * @return \CodeIgniter\CodeIgniter\View\View
 */
/*******  0c7386b3-199a-4f6e-9054-8fbee60450cb  *******/
    public function cierreMes()
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();

        // Rango del mes seleccionado
        $mesAnio = $request->getGet('mes') ?? date('Y-m');
        $fechaInicio = $mesAnio . '-01';
        $fechaFin    = date('Y-m-t', strtotime($fechaInicio));

        // 1. Totales Generales KPI (Tarjetas superiores)
        $kpis = $db->table('seguimiento_socios')
            ->select('
                SUM(citas_realizadas) as total_citas_realizadas,
                SUM(citas_programadas) as total_citas_programadas,
                SUM(propuestas) as total_propuestas,
                SUM(cierres) as total_cierres,
                SUM(asociados_nuevos) as total_nuevos_asociados
            ')
            ->where('semana_fecha >=', $fechaInicio)
            ->where('semana_fecha <=', $fechaFin)
            ->get()->getRowArray();

        // 2. Resumen acumulado por Asociado
        $resumenPorAsociado = $db->table('seguimiento_socios seg')
            ->select('
                s.id as socio_id,
                s.nombre as socio_nombre,
                s.tipo_participacion,
                SUM(seg.citas_realizadas) as citas_realiz,
                SUM(seg.citas_programadas) as citas_progr,
                SUM(seg.propuestas) as propuestas,
                SUM(seg.cierres) as cierres,
                SUM(seg.asociados_nuevos) as asoc_nvo
            ')
            ->join('socios s', 's.id = seg.socio_id', 'inner')
            ->where('seg.semana_fecha >=', $fechaInicio)
            ->where('seg.semana_fecha <=', $fechaFin)
            ->groupBy('s.id')
            ->orderBy('citas_realiz', 'DESC')
            ->get()->getResultArray();

        // 3. Registros individuales de la Bitácora de Seguimiento
        $bitacora = $db->table('seguimiento_socios seg')
            ->select('
                seg.*,
                s.nombre as socio_nombre,
                p.company_name as cliente_nombre
            ')
            ->join('socios s', 's.id = seg.socio_id', 'left')
            ->join('prospects p', 'p.id = seg.prospect_id', 'left')
            ->where('seg.semana_fecha >=', $fechaInicio)
            ->where('seg.semana_fecha <=', $fechaFin)
            ->orderBy('seg.semana_fecha', 'DESC')
            ->get()->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'mesAnio'            => $mesAnio,
            'kpis'               => $kpis,
            'resumenPorAsociado' => $resumenPorAsociado,
            'bitacora'           => $bitacora,
            'socios'             => $db->table('socios')->orderBy('nombre', 'ASC')->get()->getResultArray(),
            'prospects'          => $db->table('prospects')->select('id, company_name')->orderBy('company_name', 'ASC')->get()->getResultArray()
        ]);

        return view('socios/cierre_mes', $data);
    }

    public function guardarSeguimiento()
    {
        $db = \Config\Database::connect();

        $citasRealizadas = (int) $this->request->getPost('citas_realizadas');
        $citasProgram    = (int) $this->request->getPost('citas_programadas');
        $propuestas      = (int) $this->request->getPost('propuestas');
        $cierres         = (int) $this->request->getPost('cierres');
        $asocNuevos      = (int) $this->request->getPost('asociados_nuevos');
        $semanaFecha     = $this->request->getPost('semana_fecha');

        $data = [
            'socio_id'              => $this->request->getPost('socio_id'),
            'prospect_id'           => $this->request->getPost('prospect_id') ?: null,
            'semana_fecha'          => $semanaFecha,
            'citas_realizadas'      => $citasRealizadas,
            'citas_programadas'     => $citasProgram,
            'propuestas'            => $propuestas,
            'cierres'               => $cierres,
            'asociados_nuevos'      => $asocNuevos,
            'nombre_asociado_nuevo' => $this->request->getPost('nombre_asociado_nuevo'),
            'red_madre'             => $this->request->getPost('red_madre'),
            'created_by'            => session()->get('id')
        ];

        $db->table('seguimiento_socios')->insert($data);

        // --- Sincronización automática con Capturas Comerciales ---
        $canalAsociados = 'Otros'; // O el canal correspondiente en tu sistema
        $mesCorte = date('Y-m-d', strtotime($semanaFecha));

        $sumaSemanal = $db->table('seguimiento_socios')
            ->where('semana_fecha', $semanaFecha)
            ->selectSum('citas_realizadas', 'total_citas')
            ->get()->getRowArray()['total_citas'] ?? 0;

        $existeCaptura = $db->table('capturas_comerciales')
            ->where('fecha', $semanaFecha)
            ->where('canal', $canalAsociados)
            ->get()->getRowArray();

        if ($existeCaptura) {
            $db->table('capturas_comerciales')
               ->where('id', $existeCaptura['id'])
               ->update(['citas_asociados' => $sumaSemanal]);
        }

        return redirect()->to('/socios/cierre-mes')->with('msg', 'Seguimiento registrado y métricas actualizadas correctamente.');
    }
}