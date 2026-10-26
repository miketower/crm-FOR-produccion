<?php

namespace App\Controllers;

use App\Models\CorteSemanalModel;

class CortesSemanal extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();

        // Extraemos los datos de los catálogos activos ordenados de forma descendente
        $data = [
            'titulo'           => 'Carga Semanal de Ingresos',
            'esquemasServicio' => $db->table('cat_esquemas_servicio')
                                    ->where('estatus', 1)
                                    ->orderBy('id', 'DESC') // <-- Ordena por ID del más nuevo al más viejo
                                    ->get()
                                    ->getResultArray(),
                                    
            'tiposVenta'       => $db->table('cat_tipos_ventas')
                                    ->where('estatus', 1)
                                    ->orderBy('id', 'DESC') // <-- Ordena por ID del más nuevo al más viejo
                                    ->get()
                                    ->getResultArray()
        ];

        return view('cortes/nuevo', $data);
    }

    public function guardar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $model = new CorteSemanalModel();

        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin    = $this->request->getPost('fecha_fin');
        $periodo     = date('Y-m', strtotime($fechaFin));
        $serviciosPost = $this->request->getPost('servicios');

        if (empty($serviciosPost) || !is_array($serviciosPost)) {
            return redirect()->back()->with('error', 'No se recibieron datos válidos en el formulario.');
        }

        $dataAInsertar = [];

        foreach ($serviciosPost as $item) {
            $facturacion = (float) str_replace(',', '', $item['facturacion'] ?? 0);
            $ibNuevo     = (float) str_replace(',', '', $item['ib_nuevo'] ?? 0);
            $ibTotal     = (float) str_replace(',', '', $item['ib_total'] ?? 0);

            $dataAInsertar[] = [
                'fecha_inicio'            => $fechaInicio,
                'fecha_fin'               => $fechaFin,
                'periodo'                 => $periodo,
                'cat_esquema_servicio_id' => $item['cat_esquema_servicio_id'],
                'cat_tipo_venta_id'       => $item['cat_tipo_venta_id'],
                'facturacion'             => $facturacion,
                'ib_nuevo'                => $ibNuevo,
                'ib_total'                => $ibTotal
            ];
        }

        if ($model->insertBatch($dataAInsertar)) {
            return redirect()->to('/dashboard')->with('success', 'El avance semanal se guardó correctamente.');
        }

        return redirect()->back()->with('error', 'Ocurrió un error al intentar guardar los datos en la base de datos.');
    }
}