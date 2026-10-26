<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioAdmin extends Seeder
{
    public function run()
    {
        // 1. Datos del Admin
        $data = [
            'full_name'     => 'Admin',        // Tu columna es full_name
            'email'         => 'admin@victoria.com',
            'password_hash' => password_hash('12345', PASSWORD_DEFAULT), // Tu columna es password_hash
            'role'          => 'admin',                 // Tu columna es role
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        // 2. Insertar (Evitando duplicados)
        // Usamos Query Builder directo para no depender de Modelos aún
        $this->db->table('users')->where('email', 'admin@victoria.com')->delete();
        $this->db->table('users')->insert($data);
        
        echo "✅ Usuario Admin creado: admin@victoria.com | Pass: 12345 (Tabla Original)\n";
    }
}