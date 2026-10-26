<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Access;

class AccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $router = service('router');
        $controller = basename(str_replace('\\', '/', $router->controllerName()));
        $method = $router->methodName();
        $access = service('authorization');
        $access->refresh();
        // Public endpoints are intentional; vacancy display and its feed remain public.
        if ($controller === 'Auth' && in_array($method, ['index', 'login', 'logout'], true)
            || $controller === 'Vacantes' && in_array($method, ['tvPipeline', 'getVacantesJson'], true)) {
            return;
        }
        if (!$access->user()) {
            session()->remove(['id', 'name', 'email', 'role', 'isLoggedIn']);
            return $request->isAJAX()
                ? service('response')->setStatusCode(401)->setJSON(['error' => 'Inicia sesión para continuar.'])
                : redirect()->to('/');
        }
        session()->set('role', $access->user()['role']);
        if (in_array($controller, ['Usuarios', 'Accesos'], true)) {
            $allowed = $access->isAdmin();
        } elseif (in_array($controller, ['Perfil', 'Notificaciones'], true) || $controller === 'Auth' && $method === 'sinAcceso') {
            $allowed = true;
        } else {
            $permission = config(Access::class)->actions[$controller][$method] ?? null;
            $allowed = $permission !== null && $access->can($permission);
        }
        if (!$allowed) {
            return service('response')->setStatusCode(403)->setJSON(['error' => 'No tienes permiso para realizar esta acción.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
