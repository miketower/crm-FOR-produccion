<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlatformForTables extends Migration
{
    public function up()
    {
        // ========================================================================
        // 1. TABLAS CATÁLOGO (Diccionarios)
        // ========================================================================

        // 1.1 cat_esquemas_servicio
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nombre'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'abreviatura' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'estatus'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cat_esquemas_servicio');

        // 1.2 cat_tipos_socio
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nombre'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'estatus'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cat_tipos_socio');

        // 1.3 cat_esquemas_dispersion
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nombre'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'estatus'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cat_esquemas_dispersion');

        // ========================================================================
        // 2. TABLAS BASE (Los Actores)
        // ========================================================================

        // 2.1 clientes
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'razon_social'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'nombre_comercial' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'rfc'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'estatus'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('clientes');

        // 2.2 socios
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nombre'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'correo'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'telefono'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'estatus'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('socios');

        // ========================================================================
        // 3. TABLAS PIVOTE (Relaciones)
        // ========================================================================

        // 3.1 cliente_esquemas
        $this->forge->addField([
            'cliente_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'cat_esquema_servicio_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey(['cliente_id', 'cat_esquema_servicio_id'], true);
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cat_esquema_servicio_id', 'cat_esquemas_servicio', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cliente_esquemas');

        // 3.2 socio_tipos
        $this->forge->addField([
            'socio_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'cat_tipo_socio_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey(['socio_id', 'cat_tipo_socio_id'], true);
        $this->forge->addForeignKey('socio_id', 'socios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cat_tipo_socio_id', 'cat_tipos_socio', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('socio_tipos');

        // ========================================================================
        // 4. TABLAS TRANSACCIONALES (Finanzas y Reportes)
        // ========================================================================

        // 4.1 ventas_flujos
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'cliente_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'cat_esquema_servicio_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'periodo'                 => ['type' => 'VARCHAR', 'constraint' => 10], // Ej. 2026-03
            'monto_base'              => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'subtotal'                => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'comision_bruta'          => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'estatus'                 => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'              => ['type' => 'DATETIME', 'null' => true],
            'updated_at'              => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'              => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('cat_esquema_servicio_id', 'cat_esquemas_servicio', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('ventas_flujos');

        // 4.2 dispersiones_comisiones
        $this->forge->addField([
            'id'                         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'venta_flujo_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'socio_id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'cat_esquema_dispersion_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'monto_participacion'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'created_at'                 => ['type' => 'DATETIME', 'null' => true],
            'updated_at'                 => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'                 => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('venta_flujo_id', 'ventas_flujos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('socio_id', 'socios', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('cat_esquema_dispersion_id', 'cat_esquemas_dispersion', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('dispersiones_comisiones');
    }

    public function down()
    {
        // Se eliminan en orden inverso para no romper las restricciones de llaves foráneas
        $this->forge->dropTable('dispersiones_comisiones', true);
        $this->forge->dropTable('ventas_flujos', true);
        $this->forge->dropTable('socio_tipos', true);
        $this->forge->dropTable('cliente_esquemas', true);
        $this->forge->dropTable('socios', true);
        $this->forge->dropTable('clientes', true);
        $this->forge->dropTable('cat_esquemas_dispersion', true);
        $this->forge->dropTable('cat_tipos_socio', true);
        $this->forge->dropTable('cat_esquemas_servicio', true);
    }
}