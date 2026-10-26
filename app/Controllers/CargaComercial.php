<?php

namespace App\Controllers;

class CargaComercial extends BaseController
{
    /**
     * Mapeo de correo a canal comercial
     */
    private function obtenerCanalPorEmail($email)
    {
        $mapa = [
            'danya.cruz@forhumancapital.mx'   => 'Soluciones',
            'samuel.nunez@theblancgroup.com'  => 'Optimización',
            'alicia.gutierrez@forhumancapital.mx' => 'Otros',
        ];

        return $mapa[$email] ?? null;
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $userEmail = session()->get('email');
        $userRole  = session()->get('role');

        $canalAsignado = $this->obtenerCanalPorEmail($userEmail);
        $esAdmin = ($userRole === 'admin' || !$canalAsignado);

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
        $userRole  = session()->get('role');

        $canalAsignado = $this->obtenerCanalPorEmail($userEmail);
        $esAdmin = ($userRole === 'admin' || !$canalAsignado);

        if (!$esAdmin && $canalAsignado) {
            $canalFinal = $canalAsignado;
        } else {
            $canalFinal = $this->request->getPost('canal');
        }

        // Captura desglosada de citas
        $citasAsociados = (int) $this->request->getPost('citas_asociados');
        $citasDirecta   = (int) $this->request->getPost('citas_directa');
        $citasEventos   = (int) $this->request->getPost('citas_eventos');
        $citasProsper   = (int) $this->request->getPost('citas_prosper');
        
        $totalCitas = $citasAsociados + $citasDirecta + $citasEventos + $citasProsper;

        $data = [
            'user_id'         => session()->get('user_id') ?? null,
            'canal'           => $canalFinal,
            'fecha'           => $this->request->getPost('fecha'),
            'citas_asociados' => $citasAsociados,
            'citas_directa'   => $citasDirecta,
            'citas_eventos'   => $citasEventos,
            'citas_prosper'   => $citasProsper,
            'citas'           => $totalCitas,
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