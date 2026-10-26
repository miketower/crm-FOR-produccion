<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Usuarios extends BaseController
{
    // Solo permitimos entrar si es Admin
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $session = session();
        if (!$session->get('isLoggedIn')) {
            header('Location: '.base_url('/'));
            exit; 
        }
        
        // Bloqueo de seguridad: Si no es admin, lo mandamos al dashboard
        if ($session->get('role') !== 'admin') {
            header('Location: '.base_url('prospectos'));
            exit;
        }
    }

    // 1. LISTAR USUARIOS
    public function index()
    {
        $db = \Config\Database::connect();
        // Traemos todos los usuarios (menos los borrados soft-delete si tuvieras)
        $usuarios = $db->table('users')->get()->getResultArray();

        $data = array_merge($this->viewData, [
            'usuarios' => $usuarios
        ]);

        return view('usuarios/index', $data);
    }

    // 2. MOSTRAR FORMULARIO NUEVO
    public function nuevo()
    {
        return view('usuarios/crear');
    }

    // 3. GUARDAR EN BASE DE DATOS
    public function guardar()
    {
        $db = \Config\Database::connect();
        
        // Validamos que el email no exista ya
        $email = $this->request->getPost('email');
        $existe = $db->table('users')->where('email', $email)->countAllResults();

        if ($existe > 0) {
            return redirect()->back()->with('error', 'Ese correo ya está registrado.');
        }

        // Preparamos los datos
        $data = [
            'full_name'     => $this->request->getPost('full_name'),
            'email'         => $email,
            // Encriptamos la contraseña
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => $this->request->getPost('role'),
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $db->table('users')->insert($data);

        return redirect()->to('/usuarios')->with('msg', 'Usuario creado correctamente.');
    }

    // 4. ELIMINAR USUARIO
    public function eliminar($id)
    {
        $db = \Config\Database::connect();
        // Evitar que el admin se borre a sí mismo
        if($id == session()->get('id')) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $db->table('users')->where('id', $id)->delete();
        return redirect()->back()->with('msg', 'Usuario eliminado.');
    }
}