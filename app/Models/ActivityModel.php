<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityModel extends Model
{
    protected $table            = 'prospect_activities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Campos que permitimos insertar/actualizar
    protected $allowedFields    = [
        'prospect_id', 
        'user_id', 
        'activity_type', 
        'subject', 
        'notes', 
        'due_date', 
        'is_completed'
    ];

    // Manejo automático de fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Función personalizada para traer el historial completo de un prospecto.
     * Hace un JOIN con la tabla 'users' para saber quién escribió la nota.
     */
    public function getHistorialByProspect($prospectId)
    {
        return $this->select('prospect_activities.*, users.full_name as creator_name')
                    ->join('users', 'users.id = prospect_activities.user_id', 'left')
                    ->where('prospect_activities.prospect_id', $prospectId)
                    ->orderBy('prospect_activities.created_at', 'DESC') // Ordenar de lo más reciente a lo más viejo
                    ->findAll();
    }
    
    /**
     * Función opcional para el futuro: Traer las tareas pendientes de un usuario
     */
    public function getTareasPendientes($userId)
    {
        return $this->select('prospect_activities.*, prospects.company_name')
                    ->join('prospects', 'prospects.id = prospect_activities.prospect_id')
                    ->where('prospect_activities.user_id', $userId)
                    ->where('prospect_activities.is_completed', 0)
                    ->where('prospect_activities.due_date IS NOT NULL')
                    ->orderBy('prospect_activities.due_date', 'ASC')
                    ->findAll();
    }
}