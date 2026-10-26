<?php

namespace App\Controllers;

class Forecast extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        
        $data['titulo'] = "Configuración de Proyecciones (Forecast)";
        return view('forecast/index', $data);
    }

    // Método AJAX para obtener los datos de un año específico
    public function getYearData($anio)
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);
        
        $db = \Config\Database::connect();
        
        $anual      = $db->table('forecasts_anual')->where('anio', $anio)->get()->getRowArray();
        $trimestral = $db->table('forecasts_trimestral')->where('anio', $anio)->get()->getResultArray();
        $mensual    = $db->table('forecasts_mensual')->where('anio', $anio)->get()->getResultArray();
        
        return $this->response->setJSON([
            'anual'      => $anual,
            'trimestral' => $trimestral,
            'mensual'    => $mensual
        ]);
    }

    // Método AJAX para guardar toda la sábana
    public function save()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);
        
        $db = \Config\Database::connect();
        $anio = $this->request->getPost('anio');
        // Limpiamos las comas del formato de moneda
        $meta_anual = str_replace(',', '', $this->request->getPost('meta_anual')); 

        $db->transStart();

        // 1. Guardar Meta Anual
        $existAnual = $db->table('forecasts_anual')->where('anio', $anio)->get()->getRow();
        if ($existAnual) {
            $db->table('forecasts_anual')->where('anio', $anio)->update(['meta_anual' => $meta_anual]);
        } else {
            $db->table('forecasts_anual')->insert(['anio' => $anio, 'meta_anual' => $meta_anual]);
        }

        // 2. Guardar Proyección Trimestral (Q1 a Q4)
        for ($q = 1; $q <= 4; $q++) {
            $ingreso = str_replace(',', '', $this->request->getPost("q{$q}_ingreso") ?? 0);
            
            $existQ = $db->table('forecasts_trimestral')->where(['anio' => $anio, 'q' => $q])->get()->getRow();
            if ($existQ) {
                $db->table('forecasts_trimestral')->where(['anio' => $anio, 'q' => $q])->update(['ingreso_bruto' => $ingreso]);
            } else {
                $db->table('forecasts_trimestral')->insert(['anio' => $anio, 'q' => $q, 'ingreso_bruto' => $ingreso]);
            }
        }

        // 3. Guardar Proyección Mensual (Mes 1 a 12)
        for ($m = 1; $m <= 12; $m++) {
            $citas      = $this->request->getPost("m{$m}_citas") ?? 0;
            $propuestas = $this->request->getPost("m{$m}_propuestas") ?? 0;
            $cierres    = $this->request->getPost("m{$m}_cierres") ?? 0;

            $existM = $db->table('forecasts_mensual')->where(['anio' => $anio, 'mes' => $m])->get()->getRow();
            if ($existM) {
                $db->table('forecasts_mensual')->where(['anio' => $anio, 'mes' => $m])->update([
                    'citas' => $citas, 'propuestas' => $propuestas, 'cierres' => $cierres
                ]);
            } else {
                $db->table('forecasts_mensual')->insert([
                    'anio' => $anio, 'mes' => $m, 'citas' => $citas, 'propuestas' => $propuestas, 'cierres' => $cierres
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Hubo un problema al guardar las proyecciones.']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Proyecciones guardadas correctamente.']);
    }
}