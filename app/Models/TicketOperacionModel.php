<?php
namespace App\Models;
use CodeIgniter\Model;

class TicketOperacionModel extends Model
{
    protected $table            = 'tickets_operaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Tienes deleted_at en tu SQL
    
    protected $allowedFields    = [
        'folio', 'area_departamento', 'creador_id', 'que_sucedio', 'fecha_suceso',
        'responsable_id', 'ejecutor_id', 'acciones_implementadas', 'fecha_accion',
        'que_se_logro', 'tipo_accion', 'resultado', 'firma_supervisor_id', 'fecha_firma'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}