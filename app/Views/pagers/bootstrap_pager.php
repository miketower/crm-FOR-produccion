<?php
    // 1. Obtener de forma segura la página activa actual desde la URL (?page=X)
    $uri = current_url(true);
    $queryStrings = [];
    parse_str($uri->getQuery(), $queryStrings);
    $paginaActiva = isset($queryStrings['page']) ? (int)$queryStrings['page'] : 1;

    // 2. Obtener el total de páginas leyendo el último enlace calculado por el framework
    $totalPaginas = 1;
    $enlacesCalculados = $pager->links();
    if (!empty($enlacesCalculados)) {
        $ultimoEnlace = end($enlacesCalculados);
        $totalPaginas = (int)$ultimoEnlace['title'];
    }

    // 3. Calcular las páginas de destino para las flechas de navegación
    $prevPage = ($paginaActiva > 1) ? $paginaActiva - 1 : 1;
    $nextPage = ($paginaActiva < $totalPaginas) ? $paginaActiva + 1 : $totalPaginas;
?>

<nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0">
        
        <li class="page-item <?= ($paginaActiva <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= ($paginaActiva > 1) ? base_url(uri_string() . '?page=1') : '#' ?>" aria-label="Primera">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <li class="page-item <?= ($paginaActiva <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= ($paginaActiva > 1) ? base_url(uri_string() . '?page=' . $prevPage) : '#' ?>" aria-label="Anterior">
                <span aria-hidden="true">&lsaquo;</span>
            </a>
        </li>

        <?php foreach ($enlacesCalculados as $link): ?>
            <?php
                $paginaEvaluada = (int)$link['title'];
                // Filtramos para mostrar únicamente un entorno de 1 paso de distancia
                $esAdyacente = abs($paginaEvaluada - $paginaActiva) <= 1;
            ?>

            <?php if ($esAdyacente): ?>
                <li class="page-item <?= ($paginaEvaluada === $paginaActiva) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link['uri'] ?>">
                        <?= $paginaEvaluada ?>
                    </a>
                </li>
            <?php endif ?>
        <?php endforeach ?>

        <li class="page-item <?= ($paginaActiva >= $totalPaginas) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= ($paginaActiva < $totalPaginas) ? base_url(uri_string() . '?page=' . $nextPage) : '#' ?>" aria-label="Siguiente">
                <span aria-hidden="true">&rsaquo;</span>
            </a>
        </li>

        <li class="page-item <?= ($paginaActiva >= $totalPaginas) ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= ($paginaActiva < $totalPaginas) ? base_url(uri_string() . '?page=' . $totalPaginas) : '#' ?>" aria-label="Última">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>

    </ul>
</nav>