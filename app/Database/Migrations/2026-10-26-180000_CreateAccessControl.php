<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Config\Access;

class CreateAccessControl extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('commercial_channel', 'users')) {
            $this->forge->addColumn('users', ['commercial_channel' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true]]);
        }
        foreach (['access_roles', 'access_permissions'] as $table) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'slug' => ['type' => 'VARCHAR', 'constraint' => 100],
                'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->createTable($table, true);
        }
        foreach (['access_role_permissions' => ['role_id' => 'access_roles', 'permission_id' => 'access_permissions'],
            'access_user_roles' => ['user_id' => 'users', 'role_id' => 'access_roles']] as $table => $references) {
            foreach ($references as $column => $reference) {
                $this->forge->addField([$column => ['type' => 'INT', 'unsigned' => true]]);
                $this->forge->addForeignKey($column, $reference, 'id', 'CASCADE', 'CASCADE');
            }
            $this->forge->addKey(array_keys($references), true);
            $this->forge->createTable($table, true);
        }
        $permissions = [];
        foreach (config(Access::class)->permissions() as $slug => $name) {
            $this->db->table('access_permissions')->insert(compact('slug', 'name'));
            $permissions[$slug] = (int) $this->db->insertID();
        }
        $general = array_diff(array_keys(config(Access::class)->modules), ['vacantes', 'directorio', 'salarios']);
        $definitions = [
            'admin' => ['Administrador', array_keys(config(Access::class)->modules)],
            'manager' => ['Gerente', $general], 'sales_rep' => ['Vendedor', $general],
            'events_rep' => ['Eventos', ['eventos', 'socios', 'cierre_socios', 'prospectos']],
            'commercial_rep' => ['Comercial', ['pipeline', 'prospectos', 'calculadora', 'cotizador']],
            'operaciones' => ['Operaciones', ['operaciones']],
            'captura' => ['Captura comercial', ['carga']],
            'asociados' => ['Asociados', ['socios', 'cierre_socios', 'prospectos']],
            'reclutamiento' => ['Reclutamiento', ['vacantes']],
            'perfilamiento' => ['Perfilamiento', ['prospectos']],
            'perfilamiento_amplio' => ['Perfilamiento completo', ['prospectos']],
            'web_tools' => ['Herramientas web', ['directorio', 'salarios']],
            'optimizacion' => ['Consulta de Optimización', ['prospectos']],
        ];
        $ids = [];
        foreach ($definitions as $slug => [$name, $modules]) {
            $this->db->table('access_roles')->insert(compact('slug', 'name'));
            $ids[$slug] = (int) $this->db->insertID();
            $grants = [];
            foreach ($modules as $module) {
                $grants[] = $module . '.ver';
                $grants[] = $module . '.modificar';
            }
            if (in_array($slug, ['admin', 'manager', 'sales_rep'], true)) {
                $grants[] = 'carga.todos_canales';
                $grants[] = 'operaciones.ver_todos';
            }
            if (in_array($slug, ['admin', 'manager'], true)) {
                $grants[] = 'operaciones.firmar';
            }
            if (in_array($slug, ['admin', 'perfilamiento_amplio'], true)) {
                $grants[] = 'prospectos.ver_todos';
            }
            if (in_array($slug, ['commercial_rep', 'optimizacion'], true)) {
                $grants[] = 'prospectos.ver_optimizacion';
            }
            foreach (array_unique($grants) as $grant) {
                $this->db->table('access_role_permissions')->insert(['role_id' => $ids[$slug], 'permission_id' => $permissions[$grant]]);
            }
        }
        // One-time conversion only. Runtime authorization never depends on email addresses.
        $capture = ['danya.cruz@forhumancapital.mx' => 'Soluciones', 'samuel.nunez@theblancgroup.com' => 'Optimización', 'alicia.gutierrez@forhumancapital.mx' => 'Otros'];
        foreach ($this->db->table('users')->select('id, email, role')->get()->getResultArray() as $user) {
            $email = strtolower($user['email']);
            $assigned = isset($ids[$user['role']]) ? [$user['role']] : [];
            if ($user['role'] !== 'admin') {
                if ($user['role'] === 'operaciones') {
                    $assigned = ['operaciones'];
                } elseif ($email === 'lberlangazambrano@gmail.com') {
                    $assigned = ['perfilamiento'];
                } elseif ($email === 'carlos.sarmiento@forhumancapital.mx') {
                    $assigned = ['reclutamiento'];
                } elseif (isset($capture[$email])) {
                    $assigned = $email === 'danya.cruz@forhumancapital.mx'
                        ? ['captura', 'reclutamiento', 'perfilamiento_amplio'] : ['captura'];
                } elseif ($user['role'] !== 'events_rep' && in_array($email, ['aurora.esquivel@forhumancapital.mx', 'yuridia.juarez@forhumancapital.mx'], true)) {
                    $assigned = ['asociados'];
                }
                if (in_array($email, ['admin@forhuman.com', 'admin@victoria.com'], true)) {
                    $assigned[] = 'web_tools';
                }
                if ($email === 'david.zapata@forhumancapital.mx') {
                    $assigned[] = 'optimizacion';
                }
            }
            foreach (array_unique($assigned) as $role) {
                $this->db->table('access_user_roles')->insert(['user_id' => $user['id'], 'role_id' => $ids[$role]]);
            }
            if (isset($capture[$email])) {
                $this->db->table('users')->where('id', $user['id'])->update(['commercial_channel' => $capture[$email]]);
            }
        }
    }

    public function down()
    {
        foreach (['access_user_roles', 'access_role_permissions', 'access_permissions', 'access_roles'] as $table) {
            $this->forge->dropTable($table, true);
        }
        $this->forge->dropColumn('users', 'commercial_channel');
    }
}
