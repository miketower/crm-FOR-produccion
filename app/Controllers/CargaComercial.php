<?php

namespace App\Controllers;

use App\Libraries\Ingresos\OrigenesCitas;

class CargaComercial extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $userEmail = session()->get('email');

        $canalAsignado = service('authorization')->user()['commercial_channel'];
        $esAdmin = service('authorization')->can('carga.todos_canales');

        if (!$esAdmin && !$canalAsignado) {
            return $this->response->setStatusCode(403)->setBody('Solicita a un administrador que asigne tu canal comercial.');
        }

        // Consultamos el historial reciente
        $builder = $db->table('capturas_comerciales')
                      ->where('deleted_at', null)
                      ->orderBy('fecha', 'DESC');

        // Si no es admin, filtramos su historial por su canal asignado
        if (!$esAdmin && $canalAsignado) {
            $builder->where('canal', $canalAsignado);
        }

        $historial = $builder->get()->getResultArray();

        $data = [
            'titulo'        => 'Carga Semanal de Información Comercial',
            'canalAsignado' => $canalAsignado,
            'esAdmin'       => $esAdmin,
            'userEmail'     => $userEmail,
            'origenesCitas' => OrigenesCitas::disponibles($db),
            'historial'     => $historial
        ];

        return view('carga_comercial/index', $data);
    }

    public function guardar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $userEmail = session()->get('email');

        $canalAsignado = service('authorization')->user()['commercial_channel'];
        $esAdmin = service('authorization')->can('carga.todos_canales');

        if (!$esAdmin && !$canalAsignado) {
            return $this->response->setStatusCode(403)->setBody('Solicita a un administrador que asigne tu canal comercial.');
        }
        if (!$esAdmin && $canalAsignado) {
            $canalFinal = $canalAsignado;
        } else {
            $canalFinal = $this->request->getPost('canal');
        }

        $origenesDisponibles = OrigenesCitas::disponibles($db);
        $citas = [];
        foreach (OrigenesCitas::TODOS as $campo => $etiqueta) {
            $valor = $this->request->getPost($campo);
            if (!isset(OrigenesCitas::NUEVOS[$campo])) {
                $citas[$campo] = (int) $valor;
                continue;
            }
            $cantidad = filter_var($valor ?? 0, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0, 'max_range' => OrigenesCitas::MAXIMO_SEMANAL],
            ]);
            if ($cantidad === false) {
                return redirect()->to('/carga-comercial')->with('error', 'Las citas de BNI, QRO y MKT deben ser enteros de 0 a 10,000,000.');
            }
            if (!isset($origenesDisponibles[$campo])) {
                if ($cantidad > 0) {
                    return redirect()->to('/carga-comercial')->with('error', 'Instala los nuevos orígenes de citas antes de guardar esta carga.');
                }
                continue;
            }
            $citas[$campo] = $cantidad;
        }

        $data = $citas + [
            'user_id'         => session()->get('user_id') ?? null,
            'canal'           => $canalFinal,
            'fecha'           => $this->request->getPost('fecha'),
            'citas'           => array_sum($citas),
            'propuestas'      => (int) $this->request->getPost('propuestas'),
            'cierres'         => (int) $this->request->getPost('cierres'),
            'monto_vendido'   => (float) $this->request->getPost('monto_vendido'),
            'monto_cobrado'   => (float) $this->request->getPost('monto_cobrado'),
            'ingreso_nuevo'   => (float) $this->request->getPost('ingreso_nuevo'),
            'observaciones'   => $this->request->getPost('observaciones'),
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $db->table('capturas_comerciales')->insert($data);

        return redirect()->to('/carga-comercial')->with('msg', 'Carga semanal registrada correctamente.');
    }
}
