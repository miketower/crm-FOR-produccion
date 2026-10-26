<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaFlujoModel extends Model
{
    protected $table      = 'ventas_flujos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'cliente_id', 
        'cat_esquema_servicio_id', 
        'periodo', 
        'monto_base', 
        'subtotal', 
        'comision_bruta', 
        'estatus'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}