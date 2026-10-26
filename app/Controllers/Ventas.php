<?php

namespace App\Controllers;

class Ventas extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $model = new \CodeIgniter\Model();
        $model->setTable('ventas_flujos');

        // CORRECCIÓN CLAVE: El JOIN ahora se conecta mediante el campo 'id_externo'
        $model->select('ventas_flujos.*, c.razon_social as cliente_nombre, esq.abreviatura as esquema, tv.nombre as tipo_venta')
            ->join('clientes c', 'c.id_externo = ventas_flujos.cliente_id', 'left') 
            ->join('cat_esquemas_servicio esq', 'esq.id = ventas_flujos.cat_esquema_servicio_id', 'left')
            ->join('cat_tipos_ventas tv', 'tv.id = ventas_flujos.cat_tipo_venta_id', 'left')
            ->orderBy('ventas_flujos.created_at', 'DESC');

        // Catálogos necesarios para los select del formulario de registro (Modal)
        $clientes = $db->table('clientes')->where('estatus', 1)->orderBy('razon_social', 'ASC')->get()->getResultArray();
        $esquemas = $db->table('cat_esquemas_servicio')->where('estatus', 1)->get()->getResultArray();
        $tiposVentas = $db->table('cat_tipos_ventas')->where('estatus', 1)->get()->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'ventas'       => $model->paginate(10),
            'pager'        => $model->pager,
            'clientes'     => $clientes,
            'esquemas'     => $esquemas,
            'tipos_ventas' => $tiposVentas
        ]);

        return view('ventas/index', $data);
    }

    public function guardar()
    {
        $db = \Config\Database::connect();

        // Extraemos el periodo "YYYY-MM" basado en la fecha de venta para indexación rápida
        $fechaVendido = $this->request->getPost('fecha_vendido');
        $periodo = !empty($fechaVendido) ? date('Y-m', strtotime($fechaVendido)) : date('Y-m');

        $data = [
            'cliente_id'              => $this->request->getPost('cliente_id'),
            'cat_esquema_servicio_id' => $this->request->getPost('cat_esquema_servicio_id'),
            'cat_tipo_venta_id'       => $this->request->getPost('cat_tipo_venta_id'),
            'monto_vendido'           => $this->request->getPost('monto_vendido') ?? 0,
            'monto_cobrado'           => $this->request->getPost('monto_cobrado') ?? 0,
            'fecha_vendido'           => empty($this->request->getPost('fecha_vendido')) ? null : $this->request->getPost('fecha_vendido'),
            'fecha_cobrado'           => empty($this->request->getPost('fecha_cobrado')) ? null : $this->request->getPost('fecha_cobrado'),
            'periodo'                 => $periodo,
            'comision_bruta'          => $this->request->getPost('comision_bruta') ?? 0,
            'created_at'              => date('Y-m-d H:i:s')
        ];

        $db->table('ventas_flujos')->insert($data);

        return redirect()->to('/ventas')->with('msg', 'Flujo de venta registrado correctamente.');
    }

    public function editar($id)
    {
        $db = \Config\Database::connect();
        
        // Buscamos la venta a editar
        $venta = $db->table('ventas_flujos')->where('id', $id)->get()->getRowArray();
        
        if (!$venta) {
            return redirect()->to('/ventas')->with('msg', 'Registro de venta no encontrado.');
        }

        // Catálogos para los selects del formulario
        $clientes = $db->table('clientes')->where('estatus', 1)->orderBy('razon_social', 'ASC')->get()->getResultArray();
        $esquemas = $db->table('cat_esquemas_servicio')->where('estatus', 1)->get()->getResultArray();
        $tiposVentas = $db->table('cat_tipos_ventas')->where('estatus', 1)->get()->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'venta'        => $venta,
            'clientes'     => $clientes,
            'esquemas'     => $esquemas,
            'tipos_ventas' => $tiposVentas
        ]);

        return view('ventas/editar', $data);
    }

    public function actualizar($id)
    {
        $db = \Config\Database::connect();
        
        $fechaVendido = $this->request->getPost('fecha_vendido');
        $periodo = !empty($fechaVendido) ? date('Y-m', strtotime($fechaVendido)) : date('Y-m');

        $data = [
            'cliente_id'              => $this->request->getPost('cliente_id'),
            'cat_esquema_servicio_id' => $this->request->getPost('cat_esquema_servicio_id'),
            'cat_tipo_venta_id'       => $this->request->getPost('cat_tipo_venta_id'),
            'monto_vendido'           => $this->request->getPost('monto_vendido') ?? 0,
            'monto_cobrado'           => $this->request->getPost('monto_cobrado') ?? 0,
            'fecha_vendido'           => empty($this->request->getPost('fecha_vendido')) ? null : $this->request->getPost('fecha_vendido'),
            'fecha_cobrado'           => empty($this->request->getPost('fecha_cobrado')) ? null : $this->request->getPost('fecha_cobrado'),
            'periodo'                 => $periodo,
            'comision_bruta'          => $this->request->getPost('comision_bruta') ?? 0,
            'updated_at'              => date('Y-m-d H:i:s')
        ];

        $db->table('ventas_flujos')->update($data, ['id' => $id]);

        return redirect()->to('/ventas')->with('msg', 'Flujo de venta actualizado correctamente.');
    }
}