<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CalculadoraNomina;

class Calculadora extends BaseController
{
    // =======================================================
    // PROTECCIÓN DE SESIÓN (Integración con el CRM)
    // =======================================================
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!session()->get('isLoggedIn')) {
            header('Location: '.base_url('/'));
            exit; 
        }
    }

    public function index()
    {
        $calc = new \App\Libraries\CalculadoraNomina();

        // Parámetros actualizados para mayor precisión (UMA 2026 referencial y 30.4 días)
        $configDefault = [
            'uma' => 113.14, 'salario_minimo' => 315.08, 'isn_rate' => 3,
            'prima_riesgo' => 0.54355, 'dias_aguinaldo' => 15, 
            'dias_vacaciones' => 12, 'prima_vacacional_pct' => 25, 'dias_mes' => 30.4
        ];

        $filaDefault = [
            'cantidad' => 1, 'monto' => '', 'tipo_calculo' => 'diario', 
            'sueldo_bruto_manual' => '', 'sbc_manual' => '', // <-- Campos opcionales inicializados
            'pct_comision' => 8, 'tipo_servicio' => 'especializado',
            'modo_nomina' => 'pct', 'valor_nomina' => 40
        ];

        // =======================================================
        // INTEGRACIÓN DE VARIABLES DEL TEMPLATE (Avatar, Notifs)
        // =======================================================
        $data = array_merge($this->viewData ?? [], [
            'titulo'     => 'Calculadora de Nómina PRO',
            'grupos'     => [],
            'totales'    => ['actual' => 0, 'propuesta' => 0, 'ahorro' => 0],
            'post'       => ['config' => $configDefault, 'filas' => [$filaDefault]],
            'hubo_topes' => false
        ]);

        if ($this->request->getMethod(true) === 'POST') {
            $config = array_merge($configDefault, $this->request->getPost('config') ?? []);
            $filas  = $this->request->getPost('filas') ?? [];
            $calc->configurar($config);
            
            $totalActual = 0; $totalPropuesta = 0;
            $resultadosGrupos = [];
            $hubo_topes = false; // Bandera para el SweetAlert

            foreach ($filas as $idx => $f) {
                if (!empty($f['monto']) && $f['monto'] > 0) {
                    $modo = $f['modo_nomina'] ?? 'pct';
                    $valor = $f['valor_nomina'] ?? 40;

                    // ==========================================
                    // CÁLCULO LEGAL (CON OVERRIDE OPCIONAL)
                    // ==========================================
                    $bruto_manual = !empty($f['sueldo_bruto_manual']) ? (float)$f['sueldo_bruto_manual'] : null;
                    $sbc_manual   = !empty($f['sbc_manual']) ? (float)$f['sbc_manual'] : null;

                    if ($bruto_manual !== null && $bruto_manual > 0) {
                        // 1. El cliente capturó un Sueldo Bruto manual (se salta el Neto a Bruto)
                        $sueldo_diario = $bruto_manual / $config['dias_mes'];
                        $resLegal = $calc->calcularDesgloseDesdeDiario($sueldo_diario, $sbc_manual);
                        // Ajustamos el neto matemáticamente restando impuestos
                        $resLegal['neto'] = $resLegal['bruto'] - $resLegal['isr'] - $resLegal['imss_obrero'];
                    } else {
                        // 2. Flujo normal (sin bruto manual)
                        if ($f['tipo_calculo'] === 'diario') {
                            $resLegal = $calc->calcularDesgloseDesdeDiario($f['monto'], $sbc_manual);
                        } else {
                            // Flujo Inverso: Neto a Bruto
                            $resLegal = $calc->calcularNetoABruto($f['monto']);
                            
                            // Si detecta un SBC manual pero no un Bruto manual, 
                            // recalcula inyectando el SBC para las cuotas patronales
                            if ($sbc_manual !== null && $sbc_manual > 0) {
                                $resLegal = $calc->calcularDesgloseDesdeDiario($resLegal['salario_diario'], $sbc_manual);
                                $resLegal['neto'] = $resLegal['bruto'] - $resLegal['isr'] - $resLegal['imss_obrero'];
                            }
                        }
                    }
                    // ==========================================

                    $resPropuesta = $calc->calcularEsquemaMixto(
                        $resLegal['neto'], 
                        $valor, 
                        $f['pct_comision'], 
                        $f['tipo_servicio'], 
                        $modo
                    );

                    // ==========================================
                    // DETECCIÓN DE TOPE PARA ACTUALIZAR EL INPUT
                    // ==========================================
                    if ($modo === 'pct') {
                        $pct_efectivo = ($resPropuesta['fiscal']['neto'] / $resLegal['neto']) * 100;
                        if (round($pct_efectivo, 2) > round((float)$valor, 2)) {
                            $hubo_topes = true;
                            $filas[$idx]['valor_nomina'] = round($pct_efectivo, 2);
                        }
                    } elseif ($modo === 'sdi') {
                        $sdi_efectivo = $resPropuesta['fiscal']['sdi'];
                        if (round($sdi_efectivo, 2) > round((float)$valor, 2)) {
                            $hubo_topes = true;
                            $filas[$idx]['valor_nomina'] = round($sdi_efectivo, 2);
                        }
                    }
                    // ==========================================

                    $qty = $f['cantidad'];
                    $totalActual    += ($resLegal['gasto_real'] * $qty);
                    $totalPropuesta += ($resPropuesta['total_propuesta'] * $qty);

                    $resultadosGrupos[] = [
                        'qty' => $qty,
                        'individual_legal' => $resLegal,
                        'individual_prop'  => $resPropuesta,
                        'subtotal_legal'   => $resLegal['gasto_real'] * $qty,
                        'subtotal_prop'    => $resPropuesta['total_propuesta'] * $qty
                    ];
                }
            }
            $data['grupos'] = $resultadosGrupos;
            $data['totales'] = ['actual' => $totalActual, 'propuesta' => $totalPropuesta, 'ahorro' => $totalActual - $totalPropuesta];
            
            $data['post'] = ['config' => $config, 'filas' => $filas];
            $data['hubo_topes'] = $hubo_topes;
        }
        
        return view('calculadora_nomina/masiva', $data);
    }
}