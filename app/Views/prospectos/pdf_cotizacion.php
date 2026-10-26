<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .header { background-color: #0044cc; color: white; padding: 30px; text-align: center; }
        .container { padding: 40px; }
        .empresa-datos { margin-bottom: 30px; width: 100%; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .titulo-propuesta { color: #0044cc; font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .tabla-conceptos { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .tabla-conceptos th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 12px; text-align: left; }
        .tabla-conceptos td { padding: 12px; border-bottom: 1px solid #eee; }
        .total-box { margin-top: 30px; text-align: right; background-color: #f8f9fa; padding: 20px; border-radius: 8px; }
        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROPUESTA COMERCIAL</h1>
        <p>CRM - Soluciones Estratégicas</p>
    </div>

    <div class="container">
        <table class="empresa-datos">
            <tr>
                <td style="width: 60%;">
                    <div class="titulo-propuesta">PROSPECTO: <?= esc($p['company_name']) ?></div>
                    <p><strong>Servicio:</strong> <?= esc($p['service_name'] ?? 'Consultoría General') ?></p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <p><strong>Fecha:</strong> <?= date('d/m/Y') ?></p>
                    <p><strong>Folio:</strong> #<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></p>
                </td>
            </tr>
        </table>

        <h3>Detalles de la Propuesta</h3>
        <p>A continuación, se presentan los costos estimulados para los servicios solicitados:</p>

        <table class="tabla-conceptos">
            <thead>
                <tr>
                    <th>Concepto / Descripción</th>
                    <th style="text-align: right;">Monto (MXN)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Implementación y gestión del servicio: <?= esc($p['service_name']) ?></td>
                    <td style="text-align: right;">$<?= number_format($p['ticket_value'] ?? 0, 2) ?></td>
                </tr>
                <tr>
                    <td style="color: #666; font-size: 13px;">* Los precios están sujetos a cambios según análisis final de nómina.</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <span style="font-size: 18px; color: #666;">Inversión Estimada:</span><br>
            <span style="font-size: 28px; font-weight: bold; color: #0044cc;">
                $<?= number_format($p['ticket_value'] ?? 0, 2) ?>
            </span>
        </div>

        <div style="margin-top: 50px;">
            <p><strong>Notas importantes:</strong></p>
            <ul style="font-size: 13px; color: #555;">
                <li>Esta propuesta tiene una validez de 15 días naturales.</li>
                <li>Los servicios incluyen soporte y atención personalizada.</li>
                <li>Para proceder, favor de contactar a su asesor asignado.</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        FOR CRM - Monterrey, N.L. - www.victoriacrm.com
    </div>
</body>
</html>