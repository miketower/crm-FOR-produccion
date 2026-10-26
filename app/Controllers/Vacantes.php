<?php

namespace App\Controllers;

class Vacantes extends BaseController
{
    private function validarAcceso()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return null;
    }

    // 1. VISTA TV (PÚBLICA - Sin login para proyectar en Smart TV)
    public function tvPipeline()
    {
        return view('vacantes/tv_pipeline');
    }

    // 2. OBTENER DATOS JSON (PÚBLICO - Para que la TV lea los datos)
    public function getVacantesJson()
    {
        $db = \Config\Database::connect();
        $vacantes = $db->table('vacantes')
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC') // Muestra la más reciente arriba
                    ->get()
                    ->getResultArray();

        return $this->response->setJSON($vacantes);
    }

    public function actualizar()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $data = [
            'estatus'   => $this->request->getPost('estatus'),
            'consultor' => $this->request->getPost('consultor'),
            'etapa_num' => $this->request->getPost('etapa_num'),
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $updated = $db->table('vacantes')->where('id', $id)->update($data);

        return $this->response->setJSON(['status' => $updated ? 'success' : 'error']);
    }

    // -------------------------------------------------------------
    // LOS SIGUIENTES MÉTODOS MANTIENEN LA SEGURIDAD Y REQUIEREN LOGIN:
    // -------------------------------------------------------------

    public function index()
    {
        if ($redirect = $this->validarAcceso()) return $redirect;
        return view('vacantes/pipeline', ['titulo' => 'Pipeline de Vacantes']);
    }

    public function crear()
    {
        if ($redirect = $this->validarAcceso()) return $redirect;
        return view('vacantes/crear', ['titulo' => 'Alta de Nueva Vacante']);
    }

    public function guardar()
    {
        if ($redirect = $this->validarAcceso()) return $redirect;

        $db = \Config\Database::connect();
        $db->table('vacantes')->insert([
            'titulo_vacante'   => $this->request->getPost('titulo_vacante'),
            'tipo'             => $this->request->getPost('tipo'),
            'usuario_asignado' => $this->request->getPost('usuario_asignado'),
            'empresa_cliente'  => $this->request->getPost('empresa_cliente'),
            'fecha_apertura'   => $this->request->getPost('fecha_apertura') ?: date('Y-m-d'),
            'departamento'     => $this->request->getPost('departamento'),
            'prioridad'        => $this->request->getPost('prioridad'),
            'posiciones'       => $this->request->getPost('posiciones') ?? 1,
            'pipeline_stage'   => 'Vacante Abierta',
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/vacantes')->with('success', 'Vacante registrada correctamente');
    }

    public function updateStage()
    {
        if ($redirect = $this->validarAcceso()) return $redirect;

        $db       = \Config\Database::connect();
        $id       = $this->request->getPost('id');
        $newStage = $this->request->getPost('stage');

        $updated = $db->table('vacantes')
                      ->where('id', $id)
                      ->update([
                          'pipeline_stage' => $newStage,
                          'updated_at'     => date('Y-m-d H:i:s')
                      ]);

        return $this->response->setJSON(['status' => $updated ? 'success' : 'error']);
    }
}