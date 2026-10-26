<?php

namespace App\Controllers;

use DomainException;
use InvalidArgumentException;

/** All actions require an active administrator through AccessFilter. */
class Usuarios extends BaseController
{
    public function index()
    {
        $usuarios = \Config\Database::connect()->table('users')->select('id, full_name, email, role, is_active, created_at')->where('deleted_at', null)->get()->getResultArray();
        $roles = array_column(service('authorization')->roleCatalog(), 'name', 'id');
        foreach ($usuarios as &$usuario) {
            $usuario['role_names'] = array_map(static fn($id) => $roles[$id], service('authorization')->roleIds((int) $usuario['id']));
        }
        return view('usuarios/index', array_merge($this->viewData, ['usuarios' => $usuarios]));
    }

    public function nuevo()
    {
        return view('usuarios/crear', array_merge($this->viewData, ['roles' => service('authorization')->roleCatalog(), 'catalogo' => config(\Config\Access::class)]));
    }

    public function guardar()
    {
        if (!$this->validateData($this->request->getPost(), [
            'full_name' => 'required|max_length[100]', 'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[255]',
        ])) {
            // Do not flash the submitted password back into the session or form.
            return redirect()->to('usuarios/nuevo')->with('error', implode(' ', $this->validator->getErrors()));
        }
        $access = service('authorization');
        try {
            $ids = $access->validateRoleIds($this->request->getPost('roles') ?? []);
            $channel = $this->request->getPost('commercial_channel') ?: null;
            if (!in_array($channel, [null, 'Soluciones', 'Optimización', 'Otros'], true)) {
                throw new InvalidArgumentException('Selecciona un canal válido.');
            }
        } catch (InvalidArgumentException $exception) {
            return redirect()->to('usuarios/nuevo')->with('error', $exception->getMessage());
        }
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $db->table('users')->insert([
                'full_name' => trim($this->request->getPost('full_name')), 'email' => trim($this->request->getPost('email')),
                'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => $access->legacyRole($ids), 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'),
            ]);
            $access->setUserRoles((int) $db->insertID(), $ids, $channel);
            if (!$db->transStatus()) {
                throw new DomainException('No fue posible crear el usuario.');
            }
            $db->transCommit();
        } catch (\Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
        return redirect()->to('usuarios')->with('msg', 'Usuario creado con los accesos seleccionados.');
    }

    public function eliminar($id)
    {
        $id = (int) $id;
        if ($id === (int) session()->get('id')) {
            return redirect()->to('usuarios')->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            if ($db->DBDriver === 'MySQLi') {
                $db->query('SELECT id FROM ' . $db->prefixTable('access_roles') . " WHERE slug = 'admin' FOR UPDATE");
            }
            $admin = (int) $db->table('access_roles')->where('slug', 'admin')->get()->getRow('id');
            if (in_array($admin, service('authorization')->roleIds($id), true)
                && $db->table('access_user_roles ur')->join('users u', 'u.id = ur.user_id')->where('ur.role_id', $admin)->where('u.is_active', 1)->where('u.deleted_at', null)->countAllResults() <= 1) {
                $db->transRollback();
                return redirect()->to('usuarios')->with('error', 'Debe permanecer al menos un administrador activo.');
            }
            $db->table('access_user_roles')->where('user_id', $id)->delete();
            $db->table('users')->where('id', $id)->delete();
            if (!$db->transStatus()) {
                throw new DomainException('No fue posible eliminar el usuario.');
            }
            $db->transCommit();
        } catch (\Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
        return redirect()->to('usuarios')->with('msg', 'Usuario eliminado.');
    }
}
