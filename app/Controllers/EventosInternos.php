<?php

namespace App\Controllers;

class EventosInternos extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect(); //[cite: 2]
        
        $builder = $db->table('eventos_internos'); 
        $builder->orderBy('fecha_evento', 'ASC'); //[cite: 2]

        $data = array_merge($this->viewData ?? [], [
            'eventos' => $builder->get()->getResultArray(), //[cite: 2]
        ]);

        return view('eventos_internos/index', $data); //[cite: 2]
    }

    public function guardar()
    {
        $db = \Config\Database::connect();
        
        $invitados = $this->request->getPost('invitados');
        $invitadosString = is_array($invitados) ? implode(', ', $invitados) : '';

        // --- LÓGICA PARA SUBIR EL FLYER ---
        $flyerName = null; // Por defecto es null por si no suben nada
        $file = $this->request->getFile('flyer');
        
        // Verificamos si se subió un archivo y si es válido
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Genera un nombre aleatorio para que no choquen archivos con el mismo nombre
            $flyerName = $file->getRandomName();
            
            // Movemos el archivo a la carpeta public/uploads/eventos
            // (Asegúrate de crear esta carpeta si no existe)
            $file->move(FCPATH . 'uploads/eventos', $flyerName);
        }

        $data = [
            'titulo'             => $this->request->getPost('titulo'),
            'responsable'        => $this->request->getPost('responsable'),
            'fecha_evento'       => $this->request->getPost('fecha_evento'),
            'ubicacion'          => $this->request->getPost('ubicacion'),
            'presupuesto'        => $this->request->getPost('presupuesto'),
            'liga_registro'      => $this->request->getPost('liga_registro'),
            'objetivo'           => $this->request->getPost('objetivo'),
            'tipo_evento'        => $this->request->getPost('tipo_evento'),
            'invitados'          => $invitadosString,
            'capacidad'          => $this->request->getPost('capacidad'),
            'tipo_asistente'     => $this->request->getPost('tipo_asistente'),      // NUEVO
            'industria_dirigido' => $this->request->getPost('industria_dirigido'),  // NUEVO
            'flyer'              => $flyerName,                                     // NUEVO
            'estatus'            => 'Planeación',
            'created_at'         => date('Y-m-d H:i:s'),
        ];

        $db->table('eventos_internos')->insert($data);

        return redirect()->to('/eventos_internos')->with('msg', 'Evento interno programado correctamente.');
    }

    public function show($id)
    {
        $db = \Config\Database::connect(); //[cite: 2]
        
        $evento = $db->table('eventos_internos')->where('id', $id)->get()->getRowArray(); //[cite: 2]

        if (!$evento) {
            return redirect()->to('/eventos_internos')->with('msg', 'Evento no encontrado.'); //[cite: 2]
        }

        $data = array_merge($this->viewData ?? [], [
            'evento' => $evento //[cite: 2]
        ]);

        return view('eventos_internos/detalle', $data); //[cite: 2]
    }
}