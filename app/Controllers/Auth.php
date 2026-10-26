<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends Controller
{
    public function index()
    {
        $access = service('authorization');
        $access->refresh();
        if ($access->user()) {
            return redirect()->to($access->landingPage());
        }
        session()->remove(['id', 'name', 'email', 'role', 'isLoggedIn']);
        return view('auth/login');
    }

    public function login()
    {
        $user = \Config\Database::connect()->table('users')->where('email', $this->request->getPost('email'))
            ->where('is_active', 1)->where('deleted_at', null)->get()->getRowArray();
        if (!$user || !password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->to('/')->with('error', 'Correo o contraseña incorrectos, o cuenta inactiva.');
        }
        session()->regenerate(true);
        session()->set(['id' => $user['id'], 'name' => $user['full_name'], 'email' => $user['email'], 'role' => $user['role'], 'isLoggedIn' => true]);
        $access = service('authorization');
        $access->refresh();
        return redirect()->to($access->landingPage());
    }

    public function sinAcceso()
    {
        return view('auth/sin_acceso');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
