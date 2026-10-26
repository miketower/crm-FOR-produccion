<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Notificaciones extends Controller
{

	// ... dentro de la clase Notificaciones ...

    // PANTALLA DE HISTORIAL COMPLETO
    public function index()
    {
        // 1. Seguridad
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $userId = session()->get('id');

        // 2. Traer TODAS las notificaciones (de la más nueva a la vieja)
        $notificaciones = $db->table('notifications')
                             ->where('user_id', $userId)
                             ->orderBy('created_at', 'DESC')
                             // Opcional: ->limit(100) para no saturar si hay miles
                             ->get()
                             ->getResultArray();

        // 3. Cargar la vista
        return view('notificaciones/historial', ['notificaciones' => $notificaciones]);
    }
    
    // Obtener las últimas 5 notificaciones (JSON)
    public function obtenerUltimas()
    {
        if (!session()->get('isLoggedIn')) return $this->response->setJSON([]);

        $db = \Config\Database::connect();
        $userId = session()->get('id');

        $notificaciones = $db->table('notifications')
                             ->where('user_id', $userId)
                             ->orderBy('created_at', 'DESC')
                             ->limit(5)
                             ->get()
                             ->getResultArray();

        // Contar no leídas
        $unreadCount = $db->table('notifications')
                          ->where('user_id', $userId)
                          ->where('is_read', 0)
                          ->countAllResults();

        return $this->response->setJSON([
            'html' => '', // Lo construiremos en JS, o puedes construirlo aquí
            'data' => $notificaciones,
            'unread' => $unreadCount
        ]);
    }

    // Marcar todas como leídas
    public function marcarLeidas()
    {
        if (!session()->get('isLoggedIn')) return;
        
        $db = \Config\Database::connect();
        $userId = session()->get('id');
        
        $db->table('notifications')
           ->where('user_id', $userId)
           ->where('is_read', 0)
           ->update(['is_read' => 1]);
           
        return $this->response->setJSON(['status' => 'ok']);
    }
}