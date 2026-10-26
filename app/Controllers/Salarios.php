<?php

namespace App\Controllers;

use App\Models\SalarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Salarios extends BaseController
{
    private function cargarHelpersSalarios(): void
    {
        helper('url');
        $path = APPPATH . 'Helpers/salarios_helper.php';
        if (!is_readable($path)) {
            throw new \RuntimeException(
                'Falta el helper del módulo. Copia app/Helpers/salarios_helper.php '
                . 'en tu proyecto, respetando exactamente la ruta y el nombre del archivo.'
            );
        }
        // Carga explícita: no depende del descubrimiento ni de la caché del locator.
        require_once $path;
        foreach (['salarios_filtros', 'salarios_importe', 'salarios_incidencias',
                  'salarios_url', 'salarios_csv_texto'] as $function) {
            if (!function_exists($function)) {
                throw new \RuntimeException(
                    'app/Helpers/salarios_helper.php no define la función global ' . $function
                    . '(). Copia el helper completo del módulo, sin agregar namespace.'
                );
            }
        }
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'));
        }
        $this->cargarHelpersSalarios();
        $model = new SalarioModel();
        $filters = salarios_filtros($this->request->getGet() ?? []);
        $available = $model->disponible();
        $catalogs = $available ? $model->catalogos($filters)
            : ['guias' => [], 'areas' => [], 'subareas' => [], 'facturaciones' => []];
        // Una edición por defecto, en vez de mezclar años en los resultados.
        if ($available && $filters['guia'] === '' && $catalogs['guias']) {
            $filters['guia'] = (string) $catalogs['guias'][0]['id'];
            $catalogs = $model->catalogos($filters);
        }
        $total = $available ? $model->total($filters) : 0;
        $rawPage = $this->request->getGet('page');
        $page = is_string($rawPage) && ctype_digit($rawPage) && strlen($rawPage) <= 9
            ? max(1, (int) $rawPage) : 1;
        $pages = max(1, (int) ceil($total / 25));
        $page = min($page, $pages);
        $filters['page'] = (string) $page;
        $rows = $available ? $model->buscar($filters, 25, ($page - 1) * 25) : [];
        $guide = null;
        foreach ($catalogs['guias'] as $candidate) {
            if ((string) $candidate['id'] === $filters['guia']) {
                $guide = $candidate;
                break;
            }
        }
        return view('salarios/listado', array_merge($this->viewData ?? [], [
            'referencias' => $rows, 'filtros' => $filters, 'catalogos' => $catalogs,
            'total' => $total, 'pagina' => $page, 'paginas' => $pages,
            'disponible' => $available, 'guia' => $guide,
        ]));
    }

    public function puestos()
    {
        $this->response->setHeader('Cache-Control', 'private, no-store');
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['puestos' => []]);
        }
        $this->cargarHelpersSalarios();
        $filters = salarios_filtros($this->request->getGet() ?? []);
        if (mb_strlen($filters['buscar']) < 2) {
            return $this->response->setJSON(['puestos' => []]);
        }
        $model = new SalarioModel();
        if (!$model->disponible()) {
            return $this->response->setStatusCode(503)->setJSON(['puestos' => []]);
        }
        if ($filters['guia'] === '') {
            $catalogs = $model->catalogos($filters);
            $filters['guia'] = isset($catalogs['guias'][0]) ? (string) $catalogs['guias'][0]['id'] : '';
        }
        return $this->response->setJSON(['puestos' => $model->sugerirPuestos($filters)]);
    }

    public function perfil($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'));
        }
        $this->cargarHelpersSalarios();
        if (!ctype_digit((string) $id) || strlen((string) $id) > 9 || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound('Referencia salarial no encontrada.');
        }
        $model = new SalarioModel();
        $row = $model->disponible() ? $model->ficha((int) $id) : null;
        if (!$row) {
            throw PageNotFoundException::forPageNotFound('Referencia salarial no encontrada.');
        }
        $filters = salarios_filtros($this->request->getGet() ?? []);
        return view('salarios/perfil', array_merge($this->viewData ?? [], [
            'referencia' => $row, 'relacionadas' => $model->relacionadas($row), 'filtros' => $filters,
        ]));
    }

    public function exportar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'));
        }
        $this->cargarHelpersSalarios();
        $model = new SalarioModel();
        if (!$model->disponible()) {
            return redirect()->to(site_url('salarios'));
        }
        $filters = salarios_filtros($this->request->getGet() ?? []);
        if ($filters['guia'] === '') {
            $catalogs = $model->catalogos($filters);
            $filters['guia'] = isset($catalogs['guias'][0]) ? (string) $catalogs['guias'][0]['id'] : '';
        }
        $rows = $model->buscar($filters);
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, [
            'Año', 'Fuente', 'Área', 'Subárea', 'Puesto', 'Contexto', 'Contexto original', 'Modalidad',
            'Moneda', 'Periodicidad', 'Operador mínimo', 'Mínimo publicado', 'Med publicado',
            'Máximo publicado', 'Facturación empresa', 'Variable publicada', 'Nueva', 'Alta demanda',
            'Estado', 'Observaciones', 'Página PDF', 'Fila fuente', 'Nota de unidades',
        ], ';', '"', '');
        foreach ($rows as $row) {
            $values = [
                $row['anio'], $row['editorial'], $row['area'], $row['subarea'], $row['puesto'],
                $row['contexto'], $row['contexto_original'], $row['modalidad'] ?? 'No informada',
                $row['moneda'] ?? 'Por confirmar', $row['periodicidad'], $row['operador_minimo'],
                $row['minimo'], $row['medio'], $row['maximo'], $row['facturacion'],
                $row['variable_original'], $row['nueva'] ? 'Sí' : 'No', $row['demanda'] ? 'Sí' : 'No',
                $row['estado'], implode(' | ', salarios_incidencias($row['incidencias'])),
                $row['pagina'], $row['clave_fuente'],
                'Importes literales del PDF; unidades pendientes de validación editorial. Sin conversión.',
            ];
            fputcsv($stream, array_map('salarios_csv_texto', $values), ';', '"', '');
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        return $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="referencias-salariales.csv"')
            ->setHeader('Cache-Control', 'private, no-store')->setBody($csv);
    }
}