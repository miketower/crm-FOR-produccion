<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends Controller
{
    private $usuariosCaptura = [
        'danya.cruz@forhumancapital.mx',
        'samuel.nunez@theblancgroup.com',
        'alicia.gutierrez@forhumancapital.mx'
    ];

    // NUEVO: Definimos el arreglo para Aurora y Yuridia
    private $usuariosAsociados = [
        'aurora.esquivel@forhumancapital.mx',
        'yuridia.juarez@forhumancapital.mx'
    ];

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            $email = session()->get('email');
            $role  = session()->get('role');

            // 1. Redirección para Carlos Sarmiento (Corregido el bug de la variable)
            if ($email === 'carlos.sarmiento@forhumancapital.mx') {
                return redirect()->to('/vacantes');
            }

            // 2. Redirección para capturadores
            if (in_array($email, $this->usuariosCaptura)) {
                return redirect()->to('/carga-comercial');
            }

            // 3. NUEVO: Redirección para Aurora y Yuridia
            if (in_array($email, $this->usuariosAsociados)) {
                return redirect()->to('/socios');
            }

            // 4. Redirección para eventos (Luciana)
            if ($role === 'events_rep') {
                return redirect()->to('/eventos');
            }

            // 5. Redirección para comercial
            if ($role === 'commercial_rep') {
                return redirect()->to('/dashboard/pipeline');
            }

            // 6. Redirección por defecto
            return redirect()->to('/prospectos');
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $db = \Config\Database::connect();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $builder = $db->table('users');
        $user = $builder->where('email', $email)->get()->getRowArray();

        if ($user) {
            if (password_verify($password, $user['password_hash'])) {
                
                $ses_data = [
                    'id'         => $user['id'],
                    'name'       => $user['full_name'],
                    'email'      => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);

                if ($user['email'] === 'carlos.sarmiento@forhumancapital.mx') {
                    return redirect()->to('/vacantes');
                }

                if (in_array($user['email'], $this->usuariosCaptura)) {
                    return redirect()->to('/carga-comercial');
                }

                // NUEVO: Redirección al loguearse para Aurora y Yuridia
                if (in_array($user['email'], $this->usuariosAsociados)) {
                    return redirect()->to('/socios');
                }

                if ($user['role'] === 'events_rep') {
                    return redirect()->to('/eventos');
                }
                if ($user['role'] === 'commercial_rep') {
                    return redirect()->to('/dashboard/pipeline');
                }

                return redirect()->to('/prospectos');
            } else {
                $session->setFlashdata('error', 'Contraseña incorrecta.');
                return redirect()->back();
            }
        } else {
            $session->setFlashdata('error', 'Usuario no encontrado.');
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}