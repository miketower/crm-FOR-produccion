<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { width: 150px; }
        .section-title { background: #f4f4f4; padding: 5px; font-weight: bold; margin-top: 20px; border-left: 5px solid #007bff; }
        .field { margin-bottom: 10px; }
        .label { font-weight: bold; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <!-- Usamos el logo que ya tienes en el proyecto -->
        <img src="<?= base_url('assets/img/logo-white.png') ?>" class="logo">
        <h2>Alineación Estratégica</h2>
        <p>Empresa: <?= $registro['empresa'] ?> | Fecha: <?= $registro['fecha'] ?></p>
    </div>

    <div class="section-title">I. DATOS DEL PARTICIPANTE</div>
    <div class="field"><span class="label">Nombre:</span> <?= $registro['nombre_completo'] ?></div>
    <div class="field"><span class="label">Puesto:</span> <?= $registro['puesto'] ?> | <span class="label">Área:</span> <?= $registro['area'] ?></div>

    <div class="section-title">II. EXPECTATIVAS Y VISIÓN</div>
    <div class="field"><span class="label">Expectativa Personal:</span><br><?= nl2br($registro['expectativa_personal']) ?></div>
    <div class="field"><span class="label">Visión a 12 meses:</span><br><?= nl2br($registro['vision_12_meses']) ?></div>

    <div class="section-title">III. PRIORIDADES DE ORDEN</div>
    <table>
        <tr>
            <th>Operaciones</th>
            <th>Compras</th>
            <th>Marketing</th>
            <th>Recursos Humanos</th>
        </tr>
        <tr>
            <td><?= $registro['orden_operaciones'] ?></td>
            <td><?= $registro['orden_compras'] ?></td>
            <td><?= $registro['orden_marketing'] ?></td>
            <td><?= $registro['orden_rh'] ?></td>
        </tr>
    </table>

    <div class="section-title">IV. AUTODIAGNÓSTICO (Escala 1-10)</div>
    <ul>
        <li>Claridad de Objetivos: <?= $registro['perf_claridad_objetivos'] ?></li>
        <li>Accountability: <?= $registro['perf_accountability'] ?></li>
        <li>Calidad del Talento: <?= $registro['perf_calidad_talento'] ?></li>
    </ul>
</body>
</html>