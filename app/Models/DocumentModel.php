<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table            = 'prospect_documents';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['prospect_id', 'user_id', 'original_name', 'file_name', 'file_type'];
    protected $useTimestamps    = false; // Ya usamos el DEFAULT CURRENT_TIMESTAMP de SQL

    public function getDocsByProspect($prospectId)
    {
        return $this->db->table('prospect_documents')
            ->select('prospect_documents.*, users.full_name as user_name') // <--- Corregido de name a full_name
            ->join('users', 'users.id = prospect_documents.user_id', 'left')
            ->where('prospect_id', $prospectId)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
    }
}