<?php

namespace App\Models;

use CodeIgniter\Model;

class DispersionComisionModel extends Model
{
    protected $table      = 'dispersiones_comisiones';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'venta_flujo_id', 
        'socio_id', 
        'cat_esquema_dispersion_id', 
        'monto_participacion'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}