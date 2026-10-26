<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InicializarCrm extends Migration
{
    public function up()
    {
        // 1. TABLA USERS
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'ENUM', 'constraint' => ['admin', 'manager', 'sales_rep'], 'default' => 'sales_rep'],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');

        // 2. TABLA PROSPECTS
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true],
            'assigned_to' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'pipeline_stage' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Nuevo'],
            'company_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'industry' => ['type' => 'VARCHAR', 'constraint' => 100],
            'service_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'employees_count' => ['type' => 'INT'],
            'location_state' => ['type' => 'VARCHAR', 'constraint' => 50],
            'classification_group' => ['type' => 'ENUM', 'constraint' => ['A', 'B', 'C', 'D']],
            'ticket_value' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'duration_months' => ['type' => 'INT', 'default' => 0],
            'complexity_level' => ['type' => 'TINYINT', 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('created_by', 'users', 'id');
        $this->forge->addForeignKey('assigned_to', 'users', 'id');
        $this->forge->createTable('prospects');

        // 3. TABLA FINANCIAL_METRICS
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'prospect_id' => ['type' => 'INT', 'unsigned' => true],
            'total_income_charged' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'commission_percent'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'direct_costs' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'indirect_costs' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'real_margin_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            // KPIs de RH
            'payroll_vs_income_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'rotation_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'yearly_dismissals_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'yearly_hiring_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            // NUEVOS CAMPOS AGREGADOS (Auditoría)
            'surplus_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'closing_cost_percent' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('prospect_id', 'prospects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('financial_metrics');

        // 4. TABLA DECISION_PROFILE
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'prospect_id' => ['type' => 'INT', 'unsigned' => true],
            'first_contact_role' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'presentation_role' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'validator_role' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'decision_maker_role' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'psychological_profile' => ['type' => 'ENUM', 'constraint' => ['Conservador', 'Estrategico', 'Otro'], 'default' => 'Otro'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('prospect_id', 'prospects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('decision_profile');

        // 5. TABLA ANALYSIS_QUALITATIVE (VERSIÓN FINAL COMPLETA)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'prospect_id' => ['type' => 'INT', 'unsigned' => true],
            
            // Fechas
            'service_presentation_date' => ['type' => 'DATE', 'null' => true],
            'proposal_presentation_date' => ['type' => 'DATE', 'null' => true],
            'rejection_date'            => ['type' => 'DATE', 'null' => true],
            
            // Narrativa (Checklist)
            'problem_to_solve'    => ['type' => 'TEXT', 'null' => true], // 1. Problema
            'trigger_event'       => ['type' => 'TEXT', 'null' => true], // 2. Detonador
            'client_fear'         => ['type' => 'TEXT', 'null' => true], // 3. Miedo
            'closing_accelerator' => ['type' => 'TEXT', 'null' => true], // 4. Acelerador
            'real_brake'          => ['type' => 'TEXT', 'null' => true], // 5. Freno Real
            'services_sold'       => ['type' => 'TEXT', 'null' => true], // 6. Qué cerramos
            'services_rejected'   => ['type' => 'TEXT', 'null' => true], // 7. Qué NO cerramos
            
            // Cierres
            'non_closing_main_factor' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'non_closing_detail'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'general_observations' => ['type' => 'TEXT', 'null' => true], // 8. Observaciones
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('prospect_id', 'prospects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('analysis_qualitative');

        // ... (Tablas anteriores) ...

        // 6. TABLA NOTIFICACIONES
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true], // Para quién es
            'title'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'message'     => ['type' => 'TEXT', 'null' => true],
            'link'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // A dónde te lleva al dar clic
            'is_read'     => ['type' => 'TINYINT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('analysis_qualitative');
        $this->forge->dropTable('decision_profile');
        $this->forge->dropTable('financial_metrics');
        $this->forge->dropTable('prospects');
        $this->forge->dropTable('users');
    }
}