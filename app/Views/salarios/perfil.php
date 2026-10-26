<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>
<?php $r = $referencia; $incidencias = salarios_incidencias($r['incidencias']); ?>

<div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
    <div>
        <a href="<?= esc(salarios_url($filtros), 'attr') ?>" class="small text-decoration-none"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver a resultados</a>
        <h2 class="fw-bold text-dark mt-2 mb-1"><?= esc($r['puesto']) ?></h2>
        <div class="text-muted"><?= esc($r['area']) ?> · <?= esc($r['subarea']) ?></div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <span class="badge bg-light text-dark border"><?= esc($r['anio']) ?></span>
        <?php if ($r['nueva']): ?><span class="badge bg-primary">Nueva (N)</span><?php endif; ?>
        <?php if ($r['demanda']): ?><span class="badge bg-info text-dark">Mayor demanda (D)</span><?php endif; ?>
        <span class="badge <?= $incidencias ? 'bg-warning text-dark' : 'bg-secondary' ?>"><?= $incidencias ? 'Con observaciones' : 'Extraído del PDF' ?></span>
    </div>
</div>

<?php if ($incidencias): ?>
    <div class="alert alert-warning">
        <strong>Revisa esta referencia antes de utilizarla.</strong>
        <ul class="mb-0 mt-2 small"><?php foreach ($incidencias as $issue): ?><li><?= esc($issue) ?></li><?php endforeach; ?></ul>
        <p class="small mt-2 mb-0">Los importes conservan lo publicado; no se corrigieron automáticamente.</p>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4 border-start border-primary border-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    <h5 class="fw-bold mb-0">Remuneración publicada</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= esc($r['moneda'] ?? 'Moneda por confirmar') ?> · <?= esc(ucfirst($r['periodicidad'])) ?></span>
                </div>
                <div class="row g-3">
                    <?php foreach (['minimo' => 'Mínimo', 'medio' => 'Med publicado', 'maximo' => 'Máximo'] as $key => $label): ?>
                        <div class="col-md-4"><div class="bg-light rounded p-3 h-100">
                            <div class="small text-muted mb-2"><?= esc($label) ?></div>
                            <div class="fs-4 fw-bold text-dark"><?= esc(salarios_importe($r[$key], $key === 'minimo' ? $r['operador_minimo'] : '=')) ?></div>
                        </div></div>
                    <?php endforeach; ?>
                </div>
                <p class="small text-muted mt-3 mb-0">Importes literales del PDF, sin conversión ni multiplicación por mil. La unidad requiere validación editorial. Med conserva el valor publicado; “—” significa no informado.</p>
                <?php if ($r['operador_minimo'] === '>'): ?>
                    <p class="small mt-2 mb-0">La guía publica un límite inferior abierto. No informa valor medio ni máximo para esta referencia.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
            <h5 class="fw-bold mb-3">Contexto de la referencia</h5>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted small mb-2">Contexto publicado</dt><dd class="col-sm-8"><?= esc($r['contexto']) ?></dd>
                <dt class="col-sm-4 text-muted small mb-2">Zonas</dt><dd class="col-sm-8"><?= esc($r['zonas'] ? implode(' · ', $r['zonas']) : 'No desglosadas en esta tabla') ?></dd>
                <dt class="col-sm-4 text-muted small mb-2">Modalidad</dt><dd class="col-sm-8"><?= esc(['hibrido' => 'Híbrido', 'remoto' => 'Remoto'][$r['modalidad'] ?? ''] ?? 'No informada') ?></dd>
                <?php if ($r['facturacion']): ?>
                    <dt class="col-sm-4 text-muted small mb-2">Facturación anual de empresa</dt><dd class="col-sm-8"><?= esc($r['facturacion']) ?><div class="small text-muted">Millones de USD. Esta moneda corresponde a la facturación, no al sueldo.</div></dd>
                <?php endif; ?>
                <dt class="col-sm-4 text-muted small mb-2">Compensación variable</dt><dd class="col-sm-8"><?= esc($r['variable_original'] ?? 'No informada') ?><?php if ($r['variable_original']): ?><div class="small text-muted">Porcentaje publicado por separado del sueldo base.</div><?php endif; ?></dd>
            </dl>
        </div></div>

        <?php if ($relacionadas): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold">Otras referencias del mismo puesto</h5>
                    <p class="small text-muted mb-2">Verifica moneda, periodo y contexto. Estas filas no constituyen una comparación homogénea.</p>
                </div>
                <div class="table-responsive"><table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th scope="col" class="ps-4">Contexto</th><th scope="col">Moneda / periodo</th><th scope="col">Mínimo</th><th scope="col">Med</th><th scope="col">Máximo</th><th scope="col">Detalle</th></tr></thead>
                    <tbody><?php foreach ($relacionadas as $item): ?><tr>
                        <td class="ps-4 small"><?= esc($item['contexto']) ?></td>
                        <td class="small"><?= esc($item['moneda'] ?? 'Por confirmar') ?><br><?= esc($item['periodicidad']) ?></td>
                        <td class="text-nowrap"><?= esc(salarios_importe($item['minimo'], $item['operador_minimo'])) ?></td>
                        <td><?= esc(salarios_importe($item['medio'])) ?></td><td><?= esc(salarios_importe($item['maximo'])) ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= esc(salarios_url($filtros, [], 'salarios/perfil/' . $item['id']), 'attr') ?>">Ver ficha</a></td>
                    </tr><?php endforeach; ?></tbody>
                </table></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
            <h5 class="fw-bold"><i class="bi bi-journal-text text-primary me-2" aria-hidden="true"></i>Fuente y metodología</h5>
            <div class="fw-semibold mt-3"><?= esc($r['editorial']) ?></div>
            <div class="small"><?= esc($r['titulo']) ?></div>
            <div class="small mt-2">Página <strong><?= (int) $r['pagina'] ?></strong> · Fila <?= esc($r['clave_fuente']) ?></div>
            <hr>
            <p class="small text-muted"><?= esc($r['nota']) ?></p>
            <p class="small text-muted mb-0">La metodología general excluye remuneración variable, acciones y otros beneficios. Son referencias de mercado, no ofertas garantizadas.</p>
        </div></div>
        <div class="card border-0 shadow-sm"><div class="card-body p-4">
            <h6 class="fw-bold">Texto de origen</h6>
            <div class="small text-muted mb-2"><?= esc($r['puesto_original']) ?></div>
            <details><summary class="small text-primary">Ver fila extraída del PDF</summary>
                <pre class="small bg-light rounded p-2 mt-2 mb-0" style="white-space: pre-wrap; overflow-wrap: anywhere;"><?= esc($r['texto_original']) ?></pre>
            </details>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>