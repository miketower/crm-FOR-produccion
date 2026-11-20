<?php

namespace App\Libraries\Ingresos;

use CodeIgniter\Database\BaseConnection;

final class OrigenesCitas
{
    public const MAXIMO_SEMANAL = 10000000;

    public const NUEVOS = [
        'citas_bni' => 'BNI',
        'citas_qro' => 'QRO',
        'citas_mkt' => 'MKT',
    ];

    public const TODOS = [
        'citas_asociados' => 'Asoc. FOR',
        'citas_directa' => 'Venta Directa',
        'citas_eventos' => 'Eventos',
        'citas_prosper' => 'Prosper',
    ] + self::NUEVOS;

    public static function disponibles(BaseConnection $db): array
    {
        return array_intersect_key(self::TODOS, array_flip($db->getFieldNames('capturas_comerciales')));
    }
}
