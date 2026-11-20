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

    public function cierreMes()
    {
        $db = \Config\Database::connect();
        $hoy = new \DateTimeImmutable('today');
        $lunes = $hoy->modify('monday this week');
        $trimestre = $hoy->setDate((int) $hoy->format('Y'), 1 + 3 * intdiv((int) $hoy->format('n') - 1, 3), 1);
        $rangos = [
            'Esta semana' => [$lunes->format('Y-m-d'), $lunes->modify('+6 days')->format('Y-m-d')],
            'Este mes' => [$hoy->format('Y-m-01'), $hoy->format('Y-m-t')],
            'Este trimestre' => [$trimestre->format('Y-m-d'), $trimestre->modify('+3 months -1 day')->format('Y-m-d')],
            'Este año' => [$hoy->format('Y-01-01'), $hoy->format('Y-12-31')],
        ];

        $fechaInicio = $this->request->getGet('desde');
        $fechaFin = $this->request->getGet('hasta');
        $errorRango = null;
        if ($fechaInicio === null && $fechaFin === null) {
            $mes = $this->fechaValida($this->request->getGet('mes'), 'Y-m');
            [$fechaInicio, $fechaFin] = $mes
                ? [$mes->format('Y-m-01'), $mes->format('Y-m-t')]
                : $rangos['Este mes'];
        } elseif (!$this->fechaValida($fechaInicio) || !$this->fechaValida($fechaFin) || $fechaInicio > $fechaFin) {
            $errorRango = 'Selecciona dos fechas válidas con la fecha inicial anterior o igual a la final.';
            [$fechaInicio, $fechaFin] = $rangos['Este mes'];
        }

        $puedeEditar = service('authorization')->can('cierre_socios.modificar');
        $seguimiento = null;
        $editar = $this->request->getGet('editar');
        if ($editar !== null) {
            if (!$puedeEditar) {
                return $this->response->setStatusCode(403);
            }
            $id = $this->idValido($editar);
            $seguimiento = $id ? $db->table('seguimiento_socios')->where('id', $id)->get()->getRowArray() : null;
            if (!$seguimiento) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

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
            ->orderBy('seg.semana_fecha', 'DESC')
            ->orderBy('seg.id', 'DESC')
            ->get()->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'fechaInicio'        => $fechaInicio,
            'fechaFin'           => $fechaFin,
            'rangos'             => $rangos,
            'errorRango'         => $errorRango,
            'seguimiento'        => $seguimiento,
            'puedeEditar'        => $puedeEditar,
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
        return $this->persistirSeguimiento();
    }

    public function actualizarSeguimiento($id)
    {
        return $this->persistirSeguimiento((int) $id);
    }

    private function persistirSeguimiento(?int $id = null)
    {
        $db = \Config\Database::connect();
        $rango = array_intersect_key($this->request->getGet(), array_flip(['desde', 'hasta']));
        $cierre = base_url('socios/cierre-mes');
        $volver = $cierre . ($rango ? '?' . http_build_query($rango) : '');
        $formulario = $cierre . '?' . http_build_query($rango + ($id === null ? [] : ['editar' => $id]));

        $fecha = $this->request->getPost('semana_fecha');
        $socio = $this->idValido($this->request->getPost('socio_id'));
        $prospecto = $this->request->getPost('prospect_id');
        $prospectoId = $this->idValido($prospecto);
        if (!$this->fechaValida($fecha)
            || !$socio || !$db->table('socios')->where('id', $socio)->countAllResults()
            || ($prospecto !== null && $prospecto !== ''
                && (!$prospectoId || !$db->table('prospects')->where('id', $prospectoId)->countAllResults()))
        ) {
            return redirect()->to($formulario . '#form-seguimiento')->withCookies()->withInput()
                ->with('error_seguimiento', 'Indica una fecha válida, un asociado y, si aplica, un prospecto existente.');
        }

        $datos = ['semana_fecha' => $fecha, 'socio_id' => $socio, 'prospect_id' => $prospectoId ?: null];
        foreach (['citas_realizadas', 'citas_programadas', 'propuestas', 'cierres', 'asociados_nuevos'] as $campo) {
            $valor = $this->request->getPost($campo);
            $numero = $valor === null || $valor === '' ? 0
                : filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 2147483647]]);
            if ($numero === false) {
                return redirect()->to($formulario . '#form-seguimiento')->withCookies()->withInput()
                    ->with('error_seguimiento', 'Los indicadores deben ser enteros no negativos dentro del rango permitido.');
            }
            $datos[$campo] = $numero;
        }

        if (!$db->transBegin()) {
            return redirect()->to($formulario . '#form-seguimiento')->withCookies()->withInput()
                ->with('error_seguimiento', 'No se pudo iniciar el guardado. Inténtalo de nuevo.');
        }

        $tabla = $db->table('seguimiento_socios');
        $anterior = null;
        if ($id !== null) {
            $consulta = $db->DBDriver === 'MySQLi'
                ? $db->query('SELECT semana_fecha FROM ' . $db->prefixTable('seguimiento_socios') . ' WHERE id = ? FOR UPDATE', [$id])
                : $tabla->select('semana_fecha')->where('id', $id)->get();
            if ($consulta && !($anterior = $consulta->getRowArray())) {
                $db->transRollback();
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        $fechas = array_unique([$fecha, $anterior['semana_fecha'] ?? $fecha]);
        sort($fechas);
        $capturas = [];
        foreach ($fechas as $dia) {
            if (!$db->transStatus()) {
                break;
            }
            // Lock captures before changing the weekly totals, in the same date order for every request.
            $consulta = $db->DBDriver === 'MySQLi'
                ? $db->query('SELECT * FROM ' . $db->prefixTable('capturas_comerciales')
                    . " WHERE fecha = ? AND canal = 'Otros' AND deleted_at IS NULL ORDER BY id LIMIT 1 FOR UPDATE", [$dia])
                : $db->table('capturas_comerciales')->where(['fecha' => $dia, 'canal' => 'Otros', 'deleted_at' => null])
                    ->orderBy('id')->get(1);
            if ($consulta && ($captura = $consulta->getRowArray())) {
                $capturas[$dia] = $captura;
            }
        }

        if ($db->transStatus()) {
            if ($anterior) {
                $tabla->where('id', $id)->update($datos);
            } else {
                $datos['created_by'] = session()->get('id');
                $datos['nombre_asociado_nuevo'] = $this->request->getPost('nombre_asociado_nuevo');
                $datos['red_madre'] = $this->request->getPost('red_madre');
                $tabla->insert($datos);
            }
        }

        foreach ($capturas as $dia => $captura) {
            if (!$db->transStatus()) {
                break;
            }
            $this->sincronizarCitas($db, $dia, $captura);
        }

        if (!$db->transStatus() || !$db->transCommit()) {
            $db->transRollback();
            return redirect()->to($formulario . '#form-seguimiento')->withCookies()->withInput()
                ->with('error_seguimiento', 'No se pudo guardar el seguimiento. Inténtalo de nuevo.');
        }

        return redirect()->to($volver . '#bitacora-seguimiento')->withCookies()
            ->with('msg', $anterior ? 'Seguimiento actualizado.' : 'Seguimiento registrado.');
    }

    private function sincronizarCitas(\CodeIgniter\Database\BaseConnection $db, string $fecha, array $captura): void
    {
        $consulta = $db->table('seguimiento_socios')->where('semana_fecha', $fecha)
            ->selectSum('citas_realizadas', 'total')->get();
        if (!$consulta) {
            return;
        }
        $total = (int) $consulta->getRowArray()['total'];
        $db->table('capturas_comerciales')->where('id', $captura['id'])->update([
            'citas_asociados' => $total,
            'citas' => $total + (int) $captura['citas_directa'] + (int) $captura['citas_eventos'] + (int) $captura['citas_prosper'],
        ]);
    }

    private function fechaValida($valor, string $formato = 'Y-m-d'): ?\DateTimeImmutable
    {
        $fecha = is_string($valor) && !str_contains($valor, "\0")
            ? \DateTimeImmutable::createFromFormat('!' . $formato, $valor) : false;
        return $fecha && $fecha->format($formato) === $valor && (int) $fecha->format('Y') >= 1000 ? $fecha : null;
    }

    private function idValido($valor)
    {
        return filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 4294967295]]);
    }
}
