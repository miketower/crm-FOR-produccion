<?php

/** Valores de entrada escalares; evita que parámetros como area[]=x rompan consultas. */
function salarios_filtros(array $query): array
{
    $filters = [];
    foreach (['buscar' => 120, 'area' => 120, 'subarea' => 160, 'facturacion' => 80] as $key => $length) {
        $filters[$key] = isset($query[$key]) && is_string($query[$key])
            ? mb_substr(trim($query[$key]), 0, $length) : '';
    }
    $choices = [
        'zona' => ['Centro/Sur', 'Occidente/Bajío', 'Norte', 'Noroeste', 'Nacional'],
        'modalidad' => ['hibrido', 'remoto', 'no_informada'],
        'moneda' => ['MXN', 'USD', 'pendiente'],
        'periodicidad' => ['mensual', 'diario', 'anual'],
        'estado' => ['extraido', 'con_observaciones'],
        'indicador' => ['nueva', 'demanda'],
        'orden' => ['puesto', 'area', 'pagina'],
    ];
    foreach ($choices as $key => $allowed) {
        $filters[$key] = isset($query[$key]) && is_string($query[$key])
            && in_array($query[$key], $allowed, true) ? $query[$key] : '';
    }
    $guide = $query['guia'] ?? '';
    $filters['guia'] = is_string($guide) && ctype_digit($guide) && strlen($guide) <= 9
        && (int) $guide > 0 ? $guide : '';
    $page = $query['page'] ?? '';
    $filters['page'] = is_string($page) && ctype_digit($page) && strlen($page) <= 9
        && (int) $page > 0 ? $page : '';
    return $filters;
}

function salarios_importe($value, string $operator = '='): string
{
    if ($value === null || $value === '') {
        return '—';
    }
    return ($operator === '>' ? 'Más de ' : '') . number_format((float) $value, 0, '.', ',');
}

function salarios_incidencias($value): array
{
    $codes = is_string($value) ? json_decode($value, true) : [];
    $labels = [
        'rango_inconsistente' => 'El mínimo, medio y máximo publicados no siguen un orden válido.',
        'contexto_repetido' => 'El PDF contiene otra fila del mismo puesto y contexto con importes diferentes.',
        'moneda_por_confirmar' => 'La moneda no está explícita en esta fila de Noroeste; requiere confirmación.',
    ];
    $result = [];
    foreach (is_array($codes) ? $codes : [] as $code) {
        if (is_string($code) && isset($labels[$code])) {
            $result[] = $labels[$code];
        }
    }
    return $result;
}

function salarios_url(array $filters, array $changes = [], string $path = 'salarios'): string
{
    $query = array_filter(array_merge($filters, $changes), static function ($value) {
        return $value !== '' && $value !== null;
    });
    return site_url($path) . ($query ? '?' . http_build_query($query) : '');
}

/** Los CSV conservan texto del documento sin permitir fórmulas en hojas de cálculo. */
function salarios_csv_texto($value): string
{
    $text = (string) ($value ?? '');
    return preg_match('/^[\s\x{FEFF}]*[=+@\-]/u', $text) ? "'" . $text : $text;
}