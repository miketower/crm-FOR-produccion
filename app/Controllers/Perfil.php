<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Perfil extends Controller
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!session()->get('isLoggedIn')) {
            header('Location: '.base_url('/'));
            exit;
        }
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $idUsuario = session()->get('id');
        $usuario = $db->table('users')->where('id', $idUsuario)->get()->getRowArray();
        return view('usuarios/perfil', ['usuario' => $usuario]);
    }

    public function actualizar()
    {
        $db = \Config\Database::connect();
        $session = session();
        $idUsuario = $session->get('id');

        // 1. OBTENER Y LIMPIAR DATOS
        $nombre = trim($this->request->getPost('full_name'));
        $email  = trim($this->request->getPost('email'));

        // 2. VALIDACIÓN BÁSICA DE SERVIDOR
        if (empty($nombre) || empty($email)) {
            return redirect()->back()->with('error', 'El nombre y correo son obligatorios.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'El formato del correo no es válido.');
        }

        // 3. VALIDACIÓN DE DUPLICADOS (SQL)
        // Buscamos si el email ya existe en ALGUIEN MÁS (id != mi id)
        $existe = $db->table('users')
                     ->where('email', $email)
                     ->where('id !=', $idUsuario)
                     ->countAllResults();

        if ($existe > 0) {
            return redirect()->back()->with('error', 'Ese correo ya está en uso por otro usuario.');
        }

        // Datos base a actualizar
        $data = [
            'full_name' => $nombre,
            'email'     => $email,
        ];

        // 4. LÓGICA DE CONTRASEÑA
        $nuevaPass = $this->request->getPost('password');
        $confirmPass = $this->request->getPost('confirm_password');

        if (!empty($nuevaPass)) {
            // Validar longitud
            if (strlen($nuevaPass) < 6) {
                return redirect()->back()->with('error', 'La contraseña es muy corta (mínimo 6 caracteres).');
            }
            // Validar coincidencia
            if ($nuevaPass !== $confirmPass) {
                return redirect()->back()->with('error', 'Las contraseñas no coinciden.');
            }
            
            $data['password_hash'] = password_hash($nuevaPass, PASSWORD_DEFAULT);
        }

        // 5. ACTUALIZAR BD
        $db->table('users')->where('id', $idUsuario)->update($data);

        // 6. ACTUALIZAR SESIÓN
        $session->set('name', $nombre);
        $session->set('email', $email);

        return redirect()->to('/perfil')->with('msg', 'Perfil actualizado correctamente.');
    }

    public function nuevo()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/');
        
        $db = \Config\Database::connect();

        // --- LÓGICA DE INTELIGENCIA DE NEGOCIO ---
        
        // 1. Calcular Tasa de Éxito por INDUSTRIA
        // Fórmula: (Ganados de esa industria / Total de esa industria) * 100
        $sqlIndustria = "
            SELECT 
                industry, 
                COUNT(*) as total,
                SUM(CASE WHEN pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM prospects 
            WHERE industry IS NOT NULL AND industry != ''
            GROUP BY industry
        ";
        $statsIndustria = $db->query($sqlIndustria)->getResultArray();

        // Convertimos a un array simple: ['Manufactura' => 45, 'Servicios' => 20, ...] (El valor es el % de éxito)
        $pesosIndustria = [];
        foreach($statsIndustria as $stat) {
            if($stat['total'] > 0) {
                // Calculamos porcentaje de éxito
                $winRate = ($stat['ganados'] / $stat['total']) * 100;
                $pesosIndustria[$stat['industry']] = round($winRate);
            }
        }

        // 2. Calcular Tasa de Éxito por ROL DE DECISOR (Opcional, pero recomendado)
        // Saber a quién le vendemos mejor (Dueños vs RH)
        $sqlDecisor = "
            SELECT 
                decision_maker_role, 
                COUNT(*) as total,
                SUM(CASE WHEN pipeline_stage = 'Cerrado Ganado' THEN 1 ELSE 0 END) as ganados
            FROM prospects 
            JOIN decision_profile ON prospects.id = decision_profile.prospect_id
            WHERE decision_maker_role IS NOT NULL
            GROUP BY decision_maker_role
        ";
        $statsDecisor = $db->query($sqlDecisor)->getResultArray();
        
        $pesosDecisor = [];
        foreach($statsDecisor as $stat) {
            if($stat['total'] > 0) {
                $winRate = ($stat['ganados'] / $stat['total']) * 100;
                $pesosDecisor[$stat['decision_maker_role']] = round($winRate);
            }
        }

        // Enviamos estos "Pesos" a la vista
        return view('prospectos/formulario_nuevo', [
            'pesosIndustria' => $pesosIndustria,
            'pesosDecisor'   => $pesosDecisor
        ]);
    }
}