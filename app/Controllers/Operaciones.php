<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TicketOperacionModel;

class Operaciones extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!session()->get('isLoggedIn')) {
            header('Location: '.base_url('/'));
            exit; 
        }
    }

    // =======================================================
    // 1. DASHBOARD / BITÁCORA (Vista de Directores / Jefes)
    // Aquí integraremos tu HTML de la Bitácora
    // =======================================================
    public function index()
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();

        // 1. Obtener filtros
        $areaFiltro  = $request->getGet('area_filtro');
        $tipoFiltro  = $request->getGet('tipo_filtro');
        
        // Rango de fechas (Por defecto el mes actual)
        $fechaInicio = $request->getGet('fecha_inicio') ?? date('Y-m-01');
        $fechaFin    = $request->getGet('fecha_fin') ?? date('Y-m-t');
        
        $builder = $db->table('tickets_operaciones t');
        $builder->select('
            t.*, 
            c.full_name as creador_nombre,
            r.full_name as responsable_nombre,
            e.full_name as ejecutor_nombre,
            s.full_name as supervisor_nombre
        ');
        $builder->join('users c', 'c.id = t.creador_id', 'left');
        $builder->join('users r', 'r.id = t.responsable_id', 'left');
        $builder->join('users e', 'e.id = t.ejecutor_id', 'left');
        $builder->join('users s', 's.id = t.firma_supervisor_id', 'left');

        // 2. Aplicar filtros básicos
        if ($areaFiltro) $builder->where('t.area_departamento', $areaFiltro);
        if ($tipoFiltro) $builder->where('t.tipo_accion', $tipoFiltro);

        // Aplicar filtro de fechas basado en la creación del ticket
        $builder->where('DATE(t.created_at) >=', $fechaInicio);
        $builder->where('DATE(t.created_at) <=', $fechaFin);

        // 3. Seguridad: Roles
        if (!service('authorization')->can('operaciones.ver_todos')) {
            $builder->groupStart()
                    ->where('t.creador_id', session()->get('id'))
                    ->orWhere('t.responsable_id', session()->get('id'))
                    ->orWhere('t.ejecutor_id', session()->get('id'))
                    ->groupEnd();
        }

        $builder->orderBy('t.created_at', 'DESC');
        $tickets = $builder->get()->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'tickets'      => $tickets,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin
        ]);

        return view('operaciones/bitacora', $data);
    }

    // =======================================================
    // 2. FORMULARIO NUEVO TICKET (Quién reporta)
    // =======================================================
    public function nuevo()
    {
        $db = \Config\Database::connect();

        // Consultamos los usuarios activos para poder asignarlos desde la creación
        $usuarios = $db->table('users')
                    ->where('is_active', 1)
                    ->orderBy('full_name', 'ASC')
                    ->get()
                    ->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'usuarios' => $usuarios
        ]);

        return view('operaciones/formulario_nuevo', $data);
    }

    public function guardar()
{
    $db = \Config\Database::connect();
    $model = new TicketOperacionModel();

    $total = $db->table('tickets_operaciones')->countAllResults() + 1;
    $folio = 'OPS-' . date('Y') . '-' . str_pad($total, 4, '0', STR_PAD_LEFT);
    $responsableId = $this->request->getPost('responsable_id');

    $dataInsert = [
        'folio'             => $folio,
        'area_departamento' => $this->request->getPost('area_departamento'),
        'creador_id'        => session()->get('id'),
        'responsable_id'    => $responsableId,
        'que_sucedio'       => $this->request->getPost('que_sucedio'),
        'fecha_suceso'      => $this->request->getPost('fecha_suceso'),
        'resultado'         => 'proceso'
    ];

    $ticketId = $model->insert($dataInsert);

    // --- ENVIAR NOTIFICACIONES DE ASIGNACIÓN ---
    $urlTicket = base_url('operaciones/atender/' . $ticketId);

    // 1. Al usuario asignado
    $this->crearNotificacion(
        $responsableId,
        'Nuevo Ticket Asignado',
        'Se te asignó el ticket ' . $folio,
        $urlTicket
    );

    // 2. Al supervisor del usuario asignado
    $userResponsable = $db->table('users')->where('id', $responsableId)->get()->getRowArray();
    if (!empty($userResponsable['supervisor_id'])) {
        $this->crearNotificacion(
            $userResponsable['supervisor_id'],
            'Incidencia Asignada a tu Equipo',
            'Se asignó el ticket ' . $folio . ' a ' . $userResponsable['full_name'],
            $urlTicket
        );
    }

    return redirect()->to('/operaciones')->with('msg', 'Ticket reportado exitosamente. Folio: ' . $folio);
}

public function actualizar($id)
{
    $db = \Config\Database::connect();
    $model = new TicketOperacionModel();
    
    $ticket = $model->find($id);
    if (!$ticket) return redirect()->to('/operaciones')->with('error', 'Ticket no encontrado');

    $responsableId = $this->request->getPost('responsable_id') ?? $ticket['responsable_id'];
    $ejecutorId    = $this->request->getPost('ejecutor_id') ?? $ticket['ejecutor_id'];

    $dataUpdate = [
        'responsable_id'         => $responsableId,
        'ejecutor_id'            => $ejecutorId,
        'acciones_implementadas' => $this->request->getPost('acciones_implementadas'),
        'fecha_accion'           => $this->request->getPost('fecha_accion'),
        'que_se_logro'           => $this->request->getPost('que_se_logro'),
        'tipo_accion'            => $this->request->getPost('tipo_accion')
    ];

    $seFirmo = ($this->request->getPost('firmar_ticket') == '1' && service('authorization')->can('operaciones.firmar'));

    if ($seFirmo) {
        $dataUpdate['resultado']           = 'logrado';
        $dataUpdate['firma_supervisor_id'] = session()->get('id');
        $dataUpdate['fecha_firma']         = date('Y-m-d H:i:s');
    }

    $model->update($id, $dataUpdate);

    // --- ENVIAR NOTIFICACIONES DE COMPLETADO / FIRMA ---
    if ($seFirmo) {
        $urlTicket = base_url('operaciones/atender/' . $id);

        // 1. Al creador del ticket
        $this->crearNotificacion(
            $ticket['creador_id'],
            'Ticket Completado y Firmado',
            'El ticket ' . $ticket['folio'] . ' ha sido cerrado exitosamente.',
            $urlTicket
        );

        // 2. Al supervisor del responsable
        $userResponsable = $db->table('users')->where('id', $responsableId)->get()->getRowArray();
        if (!empty($userResponsable['supervisor_id'])) {
            $this->crearNotificacion(
                $userResponsable['supervisor_id'],
                'Ticket de tu Equipo Cerrado',
                'El ticket ' . $ticket['folio'] . ' de ' . $userResponsable['full_name'] . ' fue completado.',
                $urlTicket
            );
        }
    }

    $msg = $seFirmo ? 'Ticket firmado y cerrado exitosamente.' : 'Documentación actualizada correctamente.';
    return redirect()->to('/operaciones/atender/'.$id)->with('msg', $msg);
}

    // =======================================================
    // 3. ATENDER TICKET (Operaciones documenta / Supervisor firma)
    // =======================================================
    public function atender($id)
    {
        $db = \Config\Database::connect();
        
        // Obtenemos el ticket y hacemos LEFT JOIN para traer el nombre del supervisor del responsable
        $builder = $db->table('tickets_operaciones t')
                    ->select('
                        t.*,
                        sup_resp.full_name as supervisor_responsable_nombre,
                        sup_ejec.full_name as supervisor_ejecutor_nombre
                    ')
                    ->join('users resp', 'resp.id = t.responsable_id', 'left')
                    ->join('users sup_resp', 'sup_resp.id = resp.supervisor_id', 'left')
                    ->join('users ejec', 'ejec.id = t.ejecutor_id', 'left')
                    ->join('users sup_ejec', 'sup_ejec.id = ejec.supervisor_id', 'left')
                    ->where('t.id', $id);

        $ticket = $builder->get()->getRowArray();
        if (!$ticket) return redirect()->to('/operaciones')->with('error', 'Ticket no encontrado');

        // Cargamos usuarios activos con la información de su supervisor_id y nombre de supervisor
        $usuariosBuilder = $db->table('users u')
                            ->select('u.id, u.full_name, u.supervisor_id, sup.full_name as supervisor_nombre')
                            ->join('users sup', 'sup.id = u.supervisor_id', 'left')
                            ->where('u.is_active', 1)
                            ->orderBy('u.full_name', 'ASC');

        $usuarios = $usuariosBuilder->get()->getResultArray();

        $data = array_merge($this->viewData ?? [], [
            'ticket'   => $ticket,
            'usuarios' => $usuarios
        ]);

        return view('operaciones/formulario_atender', $data);
    }

    

    private function crearNotificacion($userId, $titulo, $mensaje, $url)
    {
        if (!$userId) return;
        
        $db = \Config\Database::connect();
        $db->table('notificaciones')->insert([
            'user_id'    => $userId,
            'titulo'     => $titulo,
            'mensaje'    => $mensaje,
            'url'        => $url,
            'leido'      => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function leerNotificacion($id)
    {
        $db = \Config\Database::connect();
        $notif = $db->table('notificaciones')->where('id', $id)->get()->getRowArray();

        if ($notif && $notif['user_id'] == session()->get('id')) {
            $db->table('notificaciones')->where('id', $id)->update(['leido' => 1]);
            return redirect()->to($notif['url']);
        }

        return redirect()->to('/operaciones');
    }
}