<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificacionesSeeder extends Seeder
{
    public function run()
    {
        // Asegúrate de tener un usuario con ID 1 (El Admin)
        $data = [
            [
                'user_id'    => 1,
                'title'      => 'Nuevo Prospecto Asignado',
                'message'    => 'Se te ha asignado a Aceros de México.',
                'link'       => 'prospectos',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'    => 1,
                'title'      => 'Meta Cumplida 🚀',
                'message'    => 'Has alcanzado el 50% de tu cuota mensual.',
                'link'       => 'dashboard',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'user_id'    => 1,
                'title'      => 'Recordatorio de Llamada',
                'message'    => 'Llamar al CFO de Tech Solutions.',
                'link'       => 'prospectos',
                'is_read'    => 1, // Leída
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ]
        ];

        $this->db->table('notifications')->insertBatch($data);
    }
}