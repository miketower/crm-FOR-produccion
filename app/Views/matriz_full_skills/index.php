<?= $this->extend('layout/main') ?>
<?= $this->section('contenido') ?>
<link rel="stylesheet" href="<?= esc(base_url('assets/css/matriz-full-skills.css'), 'attr') ?>">
<?php
$states = [
    'cumplido' => ['Cumplido', 'Cumplido'],
    'progreso' => ['En progreso', 'En progreso'],
    'atencion' => ['Atención', 'Requiere atención'],
];
$completed = count(array_filter($profile['indicators'], static fn($indicator) => $indicator[7] === 'cumplido'));
?>
<main class="skills-dashboard">
    <header class="skills-header">
        <div>
            <div class="skills-eyebrow"><span class="skills-eyebrow-line"></span> ESTRATEGIA <span class="skills-separator">/</span> FULL SKILLS</div>
            <h1>Matriz de Desempeño <span>y Resultados</span> <em>Full Skills</em></h1>
            <p>Una vista compartida del avance comercial y el desarrollo de cada perfil.</p>
        </div>
        <div class="skills-header-badge"><span></span> PROPUESTA VISUAL</div>
    </header>

    <div class="skills-notice" role="note">
        <span class="skills-notice-icon" aria-hidden="true">i</span>
        <div><strong>Datos de ejemplo.</strong> Las cifras y estados son ficticios y no representan resultados reales.</div>
    </div>

    <section class="skills-global" aria-labelledby="skills-global-title">
        <div class="skills-global-heading">
            <div>
                <div class="skills-kicker">UNA MISMA DIRECCIÓN</div>
                <h2 id="skills-global-title">El objetivo en común<span>.</span></h2>
                <p>Avance anual ilustrativo hacia las metas globales.</p>
            </div>
            <div class="skills-global-label"><span></span> SEGUIMIENTO ANUAL</div>
        </div>
        <div class="skills-global-grid">
            <?php foreach ([
                ['01', 'Citas realizadas', '402', '600', 67],
                ['02', 'Propuestas', '163', '250', 65],
                ['03', 'Cierres en operación', '31', '50', 62],
            ] as [$number, $label, $actual, $target, $progress]): ?>
                <div class="skills-global-metric">
                    <div class="skills-metric-top"><span><?= esc($number) ?></span><span><?= esc($progress) ?>% de la meta</span></div>
                    <div class="skills-metric-value"><?= esc($actual) ?> <small>/ <?= esc($target) ?></small></div>
                    <div class="skills-metric-name"><?= esc($label) ?></div>
                    <div class="skills-track" role="progressbar" aria-label="<?= esc($label, 'attr') ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= esc($progress, 'attr') ?>"><span style="width: <?= esc($progress, 'attr') ?>%"></span></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="skills-profiles" aria-labelledby="skills-profiles-title">
        <div class="skills-section-title">
            <div><div class="skills-section-kicker">EL EQUIPO</div><h2 id="skills-profiles-title">Desempeño por perfil</h2><p>Selecciona un perfil para explorar sus indicadores.</p></div>
            <span class="skills-count"><?= count($profiles) ?> PERFILES <span>·</span> 20 INDICADORES</span>
        </div>
        <nav class="skills-profile-grid" aria-label="Perfiles evaluados">
            <?php foreach ($profiles as $key => $item): ?>
                <a class="skills-profile-link <?= $key === $selected ? 'is-selected' : '' ?>" href="<?= esc(base_url('matriz-full-skills') . '?perfil=' . rawurlencode($key), 'attr') ?>" <?= $key === $selected ? 'aria-current="page"' : '' ?>>
                    <span class="skills-avatar skills-avatar-<?= esc($item['color'], 'attr') ?>"><?= esc($item['initials']) ?></span>
                    <span class="skills-profile-copy"><strong><?= esc($item['name']) ?></strong><small><?= esc($item['role']) ?></small></span>
                    <span class="skills-profile-arrow" aria-hidden="true"><svg viewBox="0 0 12 12" fill="none"><path d="M3 9 9 3M4 3h5v5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </section>

    <section class="skills-detail" aria-labelledby="skills-detail-title">
        <div class="skills-detail-heading">
            <div class="skills-detail-person">
                <span class="skills-avatar skills-avatar-large skills-avatar-<?= esc($profile['color'], 'attr') ?>"><?= esc($profile['initials']) ?></span>
                <div><div class="skills-section-kicker">PERFIL SELECCIONADO</div><h2 id="skills-detail-title"><?= esc($profile['name']) ?> <span>· <?= esc($profile['role']) ?></span></h2><p><?= esc($profile['intro']) ?></p></div>
            </div>
            <div class="skills-detail-summary"><strong><?= $completed ?><span> / 4</span></strong><small>indicadores cumplidos<br>en este ejemplo</small></div>
        </div>
        <div class="skills-legend" aria-label="Estados de ejemplo"><span><i class="skills-dot skills-dot-cumplido"></i>Cumplido</span><span><i class="skills-dot skills-dot-progreso"></i>En progreso</span><span><i class="skills-dot skills-dot-atencion"></i>Atención</span></div>
        <div class="skills-indicator-grid">
            <?php foreach ($profile['indicators'] as $indicator): ?>
                <?php [$type, $title, $measurement, $actual, $monthly, $annual, $contribution, $state] = $indicator; ?>
                <article class="skills-indicator">
                    <div class="skills-indicator-top"><span class="skills-type <?= $type === 'Resultado' ? 'skills-type-result' : 'skills-type-development' ?>"><?= esc($type) ?></span><span class="skills-status skills-status-<?= esc($state, 'attr') ?>"><i class="skills-dot skills-dot-<?= esc($state, 'attr') ?>"></i><?= esc($states[$state][0]) ?></span></div>
                    <h3><?= esc($title) ?></h3>
                    <p class="skills-measurement"><?= esc($measurement) ?></p>
                    <div class="skills-figures">
                        <div class="skills-figure-current"><span>EJEMPLO DEL MES</span><strong><?= esc($actual) ?></strong></div>
                        <div><span>META MENSUAL</span><strong><?= esc($monthly) ?></strong></div>
                        <div><span>META ANUAL</span><strong><?= esc($annual) ?></strong></div>
                    </div>
                    <div class="skills-contribution"><span aria-hidden="true"><svg viewBox="0 0 12 12" fill="none"><path d="M3 9 9 3M4 3h5v5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div><small>APORTA A LA META GLOBAL</small><strong><?= esc($contribution) ?></strong></div></div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <p class="skills-footer">Full Skills <span>·</span> Vista de referencia con información ficticia</p>
</main>
<?= $this->endSection() ?>
