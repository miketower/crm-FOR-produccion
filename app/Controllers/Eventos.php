<?php

namespace App\Controllers;

class Eventos extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Apuntamos a la nueva tabla unificada
        $builder = $db->table('eventos');
        $builder->orderBy('fecha_evento', 'ASC');

        $gerentes = [
            ['id' => 1, 'nombre' => 'Gerente 1 (Socio)'],
            ['id' => 2, 'nombre' => 'Gerente 2 (Socio)'],
            ['id' => 3, 'nombre' => 'Gerente 3 (Socio)'],
            ['id' => 4, 'nombre' => 'Gerente 4 (Socio)'],
        ];

        $data = array_merge($this->viewData ?? [], [
            'eventos'  => $builder->get()->getResultArray(),
            'gerentes' => $gerentes
        ]);

        return view('eventos/index', $data);
    }

    public function guardar()
    {
        $db = \Config\Database::connect();
        
        $roles = $this->request->getPost('roles_asistencia');
        $rolesString = is_array($roles) ? implode(', ', $roles) : '';

        // Recibimos todos los campos, asignando null o 0 a los que vengan vacíos
        $data = [
            'categoria_evento'       => $this->request->getPost('categoria_evento'),
            'titulo'                 => $this->request->getPost('titulo'),
            'fecha_evento'           => $this->request->getPost('fecha_evento'),
            'ubicacion'              => $this->request->getPost('ubicacion'),
            'liga_registro'          => $this->request->getPost('liga_registro'),
            'objetivo'               => $this->request->getPost('objetivo'),
            // Campos Externos
            'organizador'            => $this->request->getPost('organizador') ?: null,
            'costo'                  => $this->request->getPost('costo') ?: 0,
            'industria_segmentacion' => $this->request->getPost('industria_segmentacion') ?: null,
            'roles_asistencia'       => $rolesString,
            'alcance'                => $this->request->getPost('alcance') ?: null,
            // Campos Internos
            'responsable'            => $this->request->getPost('responsable') ?: null,
            'presupuesto'            => $this->request->getPost('presupuesto') ?: 0,
            'tipo_evento'            => $this->request->getPost('tipo_evento') ?: null,
            'invitados'              => $this->request->getPost('invitados') ?: null,
            'capacidad'              => $this->request->getPost('capacidad') ?: 0,
            'tipo_asistente'         => $this->request->getPost('tipo_asistente') ?: null,
            
            'estatus'                => 'Pendiente',
            'created_at'             => date('Y-m-d H:i:s'),
        ];

        $db->table('eventos')->insert($data);

        return redirect()->to('/eventos')->with('msg', 'Evento programado correctamente.');
    }

    public function show($id)
    {
        $db = \Config\Database::connect();
        
        $evento = $db->table('eventos')->where('id', $id)->get()->getRowArray();

        if (!$evento) {
            return redirect()->to('/eventos')->with('msg', 'Evento no encontrado.');
        }

        $data = array_merge($this->viewData ?? [], [
            'evento' => $evento
        ]);

        return view('eventos/detalle', $data);
    }

    public function editar($id)
    {
        $db = \Config\Database::connect();
        
        $evento = $db->table('eventos')->where('id', $id)->get()->getRowArray();

        if (!$evento) {
            return redirect()->to('/eventos')->with('msg', 'Evento no encontrado.');
        }

        $data = array_merge($this->viewData ?? [], [
            'evento' => $evento
        ]);

        return view('eventos/editar', $data);
    }

    public function actualizar($id)
    {
        $db = \Config\Database::connect();
        
        $roles = $this->request->getPost('roles_asistencia');
        $rolesString = is_array($roles) ? implode(', ', $roles) : '';

        $data = [
            'categoria_evento'       => $this->request->getPost('categoria_evento'),
            'titulo'                 => $this->request->getPost('titulo'),
            'fecha_evento'           => $this->request->getPost('fecha_evento'),
            'ubicacion'              => $this->request->getPost('ubicacion'),
            'liga_registro'          => $this->request->getPost('liga_registro'),
            'objetivo'               => $this->request->getPost('objetivo'),
            // Campos Externos
            'organizador'            => $this->request->getPost('organizador') ?: null,
            'costo'                  => $this->request->getPost('costo') ?: 0,
            'industria_segmentacion' => $this->request->getPost('industria_segmentacion') ?: null,
            'roles_asistencia'       => $rolesString,
            'alcance'                => $this->request->getPost('alcance') ?: null,
            // Campos Internos
            'responsable'            => $this->request->getPost('responsable') ?: null,
            'presupuesto'            => $this->request->getPost('presupuesto') ?: 0,
            'tipo_evento'            => $this->request->getPost('tipo_evento') ?: null,
            'invitados'              => $this->request->getPost('invitados') ?: null,
            'capacidad'              => $this->request->getPost('capacidad') ?: 0,
            'tipo_asistente'         => $this->request->getPost('tipo_asistente') ?: null,
            // Actualizamos estatus por si lo cambiaron
            'estatus'                => $this->request->getPost('estatus')
        ];

        $db->table('eventos')->where('id', $id)->update($data);

        return redirect()->to('/eventos/show/' . $id)->with('msg', 'Evento actualizado correctamente.');
    }
}