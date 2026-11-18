<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Access;
use DomainException;
use InvalidArgumentException;

class Authorization
{
    private ?array $currentUser = null;
    private array $roles = [];
    private array $permissions = [];
    private bool $loaded = false;

    public function __construct(private BaseConnection $db)
    {
    }

    public function refresh(): void
    {
        $this->loaded = false;
        $this->currentUser = null;
        $this->roles = $this->permissions = [];
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;
        if (!session()->get('isLoggedIn') || !($id = session()->get('id'))) {
            return;
        }
        $this->currentUser = $this->db->table('users')->select('id, full_name, email, role, commercial_channel')
            ->where('id', $id)->where('is_active', 1)->where('deleted_at', null)->get()->getRowArray();
        if (!$this->currentUser) {
            return;
        }
        $this->roles = array_column($this->db->table('access_user_roles ur')->select('r.slug')
            ->join('access_roles r', 'r.id = ur.role_id')->where('ur.user_id', $id)->get()->getResultArray(), 'slug');
        $rows = $this->db->table('access_user_roles ur')->select('p.slug')->distinct()
            ->join('access_role_permissions rp', 'rp.role_id = ur.role_id')
            ->join('access_permissions p', 'p.id = rp.permission_id')->where('ur.user_id', $id)->get()->getResultArray();
        $this->permissions = array_fill_keys(array_column($rows, 'slug'), true);
    }

    public function user(): ?array
    {
        $this->load();
        return $this->currentUser;
    }

    public function isAdmin(): bool
    {
        $this->load();
        return $this->currentUser !== null && in_array('admin', $this->roles, true);
    }

    public function can(string $permission): bool
    {
        $this->load();
        if (!$this->currentUser) {
            return false;
        }
        if ($permission === 'matriz_full_skills.ver') {
            return $this->canViewFullSkills();
        }
        if ($permission === 'usuarios.administrar') {
            return $this->isAdmin();
        }
        if (!array_key_exists($permission, config(Access::class)->permissions())) {
            return false;
        }
        return $this->isAdmin() || isset($this->permissions[$permission]);
    }

    private function canViewFullSkills(): bool
    {
        if (!$this->db->tableExists('full_skills_viewers')) {
            return false;
        }
        $emails = [
            'luciana' => 'luciana.osuna@forhumancapital.mx',
            'jorge' => 'jorgemarioblanco@forhumancapital.mx',
            'victoria' => 'victoria.torres@forhumancapital.mx',
        ];
        $slot = $this->db->table('full_skills_viewers')->select('slot')
            ->where('user_id', (int) $this->currentUser['id'])->get()->getRow('slot');
        return isset($emails[$slot]) && strtolower(trim($this->currentUser['email'])) === $emails[$slot];
    }

    public function menu(): array
    {
        $groups = [];
        foreach (config(Access::class)->modules as $key => [$label, $url, $icon, $group]) {
            if ($this->can($key . '.ver')) {
                $groups[$group][] = compact('label', 'url', 'icon');
                if ($key === 'vacantes') {
                    if ($this->can('vacantes.modificar')) {
                        $groups[$group][] = ['label' => 'Alta de Vacante', 'url' => 'vacantes/crear', 'icon' => 'bi-plus-circle'];
                    }
                    $groups[$group][] = ['label' => 'Pantalla TV', 'url' => 'vacantes/tv', 'icon' => 'bi-tv', 'blank' => true];
                }
            }
        }
        if ($this->can('matriz_full_skills.ver')) {
            $groups['Estrategia'][] = ['label' => 'Matriz Full Skills', 'url' => 'matriz-full-skills', 'icon' => 'bi-bar-chart-steps'];
        }
        if ($this->isAdmin()) {
            $groups['Configuración'][] = ['label' => 'Equipo / Usuarios', 'url' => 'usuarios', 'icon' => 'bi-people-fill'];
            $groups['Configuración'][] = ['label' => 'Roles y accesos', 'url' => 'usuarios/accesos', 'icon' => 'bi-shield-lock'];
        }
        return $groups;
    }

    public function landingPage(): string
    {
        foreach ($this->menu() as $items) {
            if ($items !== []) {
                return $items[0]['url'];
            }
        }
        return 'sin-acceso';
    }

    public function roleCatalog(): array
    {
        $roles = $this->db->table('access_roles')->orderBy('id')->get()->getResultArray();
        foreach ($roles as &$role) {
            $role['permissions'] = array_column($this->db->table('access_role_permissions rp')->select('p.slug')
                ->join('access_permissions p', 'p.id = rp.permission_id')->where('rp.role_id', $role['id'])->get()->getResultArray(), 'slug');
            if ($role['slug'] === 'admin') {
                $role['permissions'] = array_keys(config(Access::class)->permissions());
            }
        }
        return $roles;
    }

    public function roleIds(int $userId): array
    {
        return array_map('intval', array_column($this->db->table('access_user_roles')->select('role_id')
            ->where('user_id', $userId)->get()->getResultArray(), 'role_id'));
    }

    public function validateRoleIds(mixed $values): array
    {
        if (!is_array($values) || $values === [] || count(array_filter($values, static fn($v) => !is_scalar($v) || !ctype_digit((string) $v))) > 0) {
            throw new InvalidArgumentException('Selecciona al menos un rol válido.');
        }
        $ids = array_values(array_unique(array_map('intval', $values)));
        if ($this->db->table('access_roles')->whereIn('id', $ids)->countAllResults() !== count($ids)) {
            throw new InvalidArgumentException('Uno de los roles seleccionados no existe.');
        }
        return $ids;
    }

    /** Compatibility for existing record-level business rules using users.role. */
    public function legacyRole(array $ids): string
    {
        $slugs = array_column($this->db->table('access_roles')->select('slug')->whereIn('id', $ids)->get()->getResultArray(), 'slug');
        foreach (['admin', 'manager', 'commercial_rep', 'events_rep', 'operaciones', 'sales_rep'] as $slug) {
            if (in_array($slug, $slugs, true)) {
                return $slug;
            }
        }
        return 'sales_rep';
    }

    public function setUserRoles(int $userId, mixed $ids, mixed $channel): void
    {
        $ids = $this->validateRoleIds($ids);
        if (!in_array($channel, [null, 'Soluciones', 'Optimización', 'Otros'], true)) {
            throw new InvalidArgumentException('Selecciona un canal válido.');
        }
        $this->db->transBegin();
        try {
            // Serialize admin membership changes to protect the last administrator.
            if ($this->db->DBDriver === 'MySQLi') {
                $this->db->query('SELECT id FROM ' . $this->db->prefixTable('access_roles') . " WHERE slug = 'admin' FOR UPDATE");
            }
            $target = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            if (!$target) {
                throw new DomainException('El usuario no existe.');
            }
            $adminRole = (int) $this->db->table('access_roles')->where('slug', 'admin')->get()->getRow('id');
            $wasAdmin = in_array($adminRole, $this->roleIds($userId), true);
            if ($wasAdmin && !in_array($adminRole, $ids, true)) {
                if ($userId === (int) session()->get('id')) {
                    throw new DomainException('No puedes quitarte tu propio rol de administrador.');
                }
                if ($this->db->table('access_user_roles ur')->join('users u', 'u.id = ur.user_id')
                    ->where('ur.role_id', $adminRole)->where('u.is_active', 1)->where('u.deleted_at', null)->countAllResults() <= 1) {
                    throw new DomainException('Debe permanecer al menos un administrador activo.');
                }
            }
            $this->db->table('access_user_roles')->where('user_id', $userId)->delete();
            foreach ($ids as $roleId) {
                $this->db->table('access_user_roles')->insert(['user_id' => $userId, 'role_id' => $roleId]);
            }
            $this->db->table('users')->where('id', $userId)->update(['role' => $this->legacyRole($ids), 'commercial_channel' => $channel]);
            if (!$this->db->transStatus()) {
                throw new DomainException('No fue posible guardar los accesos.');
            }
            $this->db->transCommit();
            $this->refresh();
        } catch (\Throwable $exception) {
            $this->db->transRollback();
            throw $exception;
        }
    }

    public function saveRole(?int $roleId, string $name, mixed $selected): void
    {
        $name = trim($name);
        if ($name === '' || mb_strlen($name) > 80 || !is_array($selected)) {
            throw new InvalidArgumentException('Indica un nombre de hasta 80 caracteres y permisos válidos.');
        }
        $catalog = config(Access::class)->permissions();
        foreach ($selected as $key) {
            if (!is_string($key) || !isset($catalog[$key])) {
                throw new InvalidArgumentException('Se recibió un permiso desconocido.');
            }
            if (str_ends_with($key, '.modificar')) {
                $selected[] = substr($key, 0, -10) . '.ver';
            }
        }
        $this->db->transBegin();
        try {
            if ($roleId !== null) {
                $role = $this->db->table('access_roles')->where('id', $roleId)->get()->getRowArray();
                if (!$role || $role['slug'] === 'admin') {
                    throw new DomainException('El rol Administrador es fijo y conserva todos los accesos.');
                }
                $this->db->table('access_roles')->where('id', $roleId)->update(['name' => $name]);
                $this->db->table('access_role_permissions')->where('role_id', $roleId)->delete();
            } else {
                $this->db->table('access_roles')->insert(['slug' => 'custom_' . bin2hex(random_bytes(8)), 'name' => $name]);
                $roleId = (int) $this->db->insertID();
            }
            $selected = array_unique($selected);
            foreach ($selected as $key) {
                $permissionId = $this->db->table('access_permissions')->where('slug', $key)->get()->getRow('id');
                $this->db->table('access_role_permissions')->insert(['role_id' => $roleId, 'permission_id' => $permissionId]);
            }
            if (!$this->db->transStatus()) {
                throw new DomainException('No fue posible guardar el rol.');
            }
            $this->db->transCommit();
            $this->refresh();
        } catch (\Throwable $exception) {
            $this->db->transRollback();
            throw $exception;
        }
    }
}
