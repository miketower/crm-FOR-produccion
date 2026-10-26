<?php

namespace App\Controllers;

use DomainException;
use InvalidArgumentException;

class Accesos extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $access = service('authorization');
        $users = $db->table('users')->select('id, full_name, email, is_active, commercial_channel')->where('deleted_at', null)->orderBy('full_name')->get()->getResultArray();
        foreach ($users as &$user) {
            $user['role_ids'] = $access->roleIds((int) $user['id']);
        }
        return view('accesos/index', array_merge($this->viewData, [
            'usuarios' => $users, 'roles' => $access->roleCatalog(), 'catalogo' => config(\Config\Access::class),
        ]));
    }

    public function guardarUsuario($id)
    {
        try {
            service('authorization')->setUserRoles((int) $id, $this->request->getPost('roles') ?? [], $this->request->getPost('commercial_channel') ?: null);
        } catch (DomainException|InvalidArgumentException $exception) {
            return redirect()->to('usuarios/accesos')->with('error', $exception->getMessage());
        }
        return redirect()->to('usuarios/accesos')->with('msg', 'Accesos actualizados. El cambio se aplica en la siguiente solicitud del usuario.');
    }

    public function guardarRol($id = null)
    {
        try {
            service('authorization')->saveRole($id === null ? null : (int) $id, (string) $this->request->getPost('name'), $this->request->getPost('permissions') ?? []);
        } catch (DomainException|InvalidArgumentException $exception) {
            return redirect()->to('usuarios/accesos')->with('error', $exception->getMessage());
        }
        return redirect()->to('usuarios/accesos')->with('msg', 'Rol guardado. Sus usuarios reciben los nuevos permisos en la siguiente solicitud.');
    }
}
