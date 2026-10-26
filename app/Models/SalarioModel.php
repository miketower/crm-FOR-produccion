<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class SalarioModel extends Model
{
    protected $table = 'sal_referencias';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [];

    public function disponible(): bool
    {
        return $this->db->tableExists('sal_guias')
            && $this->db->tableExists('sal_referencias')
            && $this->db->tableExists('sal_referencia_zonas');
    }

    private function consulta(array $filters, bool $soloPuestos = false): BaseBuilder
    {
        $builder = $this->db->table('sal_referencias r')
            ->select($soloPuestos ? 'r.puesto' : 'r.*, g.anio, g.titulo, g.editorial')
            ->join('sal_guias g', 'g.id = r.guia_id');
        foreach (['guia' => 'r.guia_id', 'area' => 'r.area', 'subarea' => 'r.subarea',
                  'periodicidad' => 'r.periodicidad', 'estado' => 'r.estado',
                  'facturacion' => 'r.facturacion'] as $filter => $column) {
            if (($filters[$filter] ?? '') !== '') {
                $builder->where($column, $filters[$filter]);
            }
        }
        if (($filters['buscar'] ?? '') !== '') {
            $builder->groupStart()->like('r.puesto', $filters['buscar'])
                ->orLike('r.puesto_original', $filters['buscar'])->groupEnd();
        }
        if (($filters['zona'] ?? '') !== '') {
            // La relación impide duplicar una referencia que cubre varias zonas.
            $builder->join('sal_referencia_zonas z', 'z.referencia_id = r.id')
                ->where('z.zona', $filters['zona']);
        }
        if (($filters['moneda'] ?? '') !== '') {
            $builder->where('r.moneda', $filters['moneda'] === 'pendiente' ? null : $filters['moneda']);
        }
        if (($filters['modalidad'] ?? '') !== '') {
            $builder->where('r.modalidad', $filters['modalidad'] === 'no_informada' ? null : $filters['modalidad']);
        }
        if (in_array($filters['indicador'] ?? '', ['nueva', 'demanda'], true)) {
            $builder->where('r.' . $filters['indicador'], 1);
        }
        return $builder;
    }

    public function total(array $filters): int
    {
        return $this->consulta($filters)->countAllResults();
    }

    public function sugerirPuestos(array $filters): array
    {
        if (mb_strlen(trim($filters['buscar'] ?? '')) < 2) {
            return [];
        }
        return array_column($this->consulta($filters, true)->distinct()
            ->orderBy('r.puesto', 'ASC')->limit(10)->get()->getResultArray(), 'puesto');
    }

    public function buscar(array $filters, ?int $limit = null, int $offset = 0): array
    {
        $orders = ['puesto' => 'r.puesto', 'area' => 'r.area', 'pagina' => 'r.pagina'];
        $builder = $this->consulta($filters)
            ->orderBy($orders[$filters['orden'] ?? ''] ?? 'r.puesto', 'ASC')
            ->orderBy('r.id', 'ASC');
        if ($limit !== null) {
            $builder->limit($limit, max(0, $offset));
        }
        return $builder->get()->getResultArray();
    }

    public function catalogos(array $filters): array
    {
        $result = [
            'guias' => $this->db->table('sal_guias')->orderBy('anio', 'DESC')->orderBy('id', 'DESC')->get()->getResultArray(),
        ];
        foreach (['areas' => 'area', 'subareas' => 'subarea', 'facturaciones' => 'facturacion'] as $key => $column) {
            $builder = $this->db->table('sal_referencias')->select($column)->distinct()
                ->where($column . ' IS NOT NULL')->orderBy($column, 'ASC');
            if (($filters['guia'] ?? '') !== '') {
                $builder->where('guia_id', $filters['guia']);
            }
            if ($key === 'subareas' && ($filters['area'] ?? '') !== '') {
                $builder->where('area', $filters['area']);
            }
            $result[$key] = array_column($builder->get()->getResultArray(), $column);
        }
        return $result;
    }

    public function ficha(int $id): ?array
    {
        $row = $this->db->table('sal_referencias r')
            ->select('r.*, g.anio, g.titulo, g.editorial, g.archivo_fuente, g.sha256_pdf, g.nota')
            ->join('sal_guias g', 'g.id = r.guia_id')->where('r.id', $id)->get()->getRowArray();
        if (!$row) {
            return null;
        }
        $row['zonas'] = array_column($this->db->table('sal_referencia_zonas')
            ->select('zona')->where('referencia_id', $id)->orderBy('zona')->get()->getResultArray(), 'zona');
        return $row;
    }

    public function relacionadas(array $row): array
    {
        return $this->db->table('sal_referencias')->where('guia_id', $row['guia_id'])
            ->where('area', $row['area'])->where('subarea', $row['subarea'])
            ->where('puesto', $row['puesto'])->where('id !=', $row['id'])
            ->orderBy('pagina')->orderBy('id')->get()->getResultArray();
    }
}