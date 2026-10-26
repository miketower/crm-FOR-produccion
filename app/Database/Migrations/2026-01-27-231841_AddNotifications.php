<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotifications extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'message'     => ['type' => 'TEXT', 'null' => true],
            'link'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_read'     => ['type' => 'TINYINT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        
        // Llave primaria
        $this->forge->addPrimaryKey('id');
        
        // Crear la tabla
        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}