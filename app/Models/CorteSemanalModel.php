<?php

namespace App\Models;

use CodeIgniter\Model;

class CorteSemanalModel extends Model
{
    protected $table            = 'cortes_semanales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Actualizamos los campos permitidos
    protected $allowedFields    = [
        'fecha_inicio', 
        'fecha_fin', 
        'periodo', 
        'cat_esquema_servicio_id', 
        'cat_tipo_venta_id', 
        'facturacion', 
        'ib_nuevo', 
        'ib_total'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}