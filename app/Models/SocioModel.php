<?php

namespace App\Models;

use CodeIgniter\Model;

class SocioModel extends Model
{
    protected $table      = 'socios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'correo', 'telefono', 'estatus'];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}