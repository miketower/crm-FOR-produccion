<?php

namespace App\Libraries;

class CalculadoraNomina
{
    private $uma = 117.31;
    private $salario_minimo = 315.08;
    private $isn_rate = 0.03;
    private $prima_riesgo = 0.0054355; 
    private $dias_aguinaldo = 15;
    private $dias_vacaciones = 12;
    private $prima_vacacional_pct = 0.25;
    private $dias_mes = 30;
    private $factor_integracion = 1.0493;

    // --- SETTERS PARA RECIBIR CONFIGURACIÓN DEL FORMULARIO ---
    public function setIsnRate($tasa) {
        $this->isn_rate = (float)$tasa / 100; // Si mandan "3" del form, lo hace 0.03
    }

    public function setPrimaRiesgo($prima) {
        $this->prima_riesgo = (float)$prima / 100; // Si mandan "2.5984", lo hace 0.025984
    }

    private $tabla_isr = [
        ['inf' => 0.01,       'sup' => 844.59,    'cuota' => 0,        'pct' => 0.0192],
        ['inf' => 844.60,     'sup' => 7168.51,   'cuota' => 16.22,    'pct' => 0.0640],
        ['inf' => 7168.52,    'sup' => 12598.02,  'cuota' => 420.95,   'pct' => 0.1088],
        ['inf' => 12598.03,   'sup' => 14644.64,  'cuota' => 1011.68,  'pct' => 0.1600],
        ['inf' => 14644.65,   'sup' => 17533.64,  'cuota' => 1339.14,  'pct' => 0.1792],
        ['inf' => 17533.65,   'sup' => 35362.83,  'cuota' => 1856.84,  'pct' => 0.2136],
        ['inf' => 35362.84,   'sup' => 55736.68,  'cuota' => 5665.16,  'pct' => 0.2352],
        ['inf' => 55736.69,   'sup' => 106410.50, 'cuota' => 10457.09, 'pct' => 0.3000],
        ['inf' => 106410.51,  'sup' => 141880.66, 'cuota' => 25659.23, 'pct' => 0.3200],
        ['inf' => 141880.67,  'sup' => 425641.99, 'cuota' => 37009.69, 'pct' => 0.3400],
        ['inf' => 425642.00,  'sup' => 99999999,  'cuota' => 133488.54,'pct' => 0.3500],
    ];

    public function configurar($params)
    {
        $this->uma                  = (float)($params['uma'] ?? 117.31);
        $this->salario_minimo       = (float)($params['salario_minimo'] ?? 315.08);
        $this->isn_rate             = (float)($params['isn_rate'] ?? 3) / 100;
        $this->prima_riesgo         = (float)($params['prima_riesgo'] ?? 0.54355) / 100;
        $this->dias_aguinaldo       = (float)($params['dias_aguinaldo'] ?? 15);
        $this->dias_vacaciones      = (float)($params['dias_vacaciones'] ?? 12);
        $this->prima_vacacional_pct = (float)($params['prima_vacacional_pct'] ?? 25) / 100;
        $this->dias_mes             = (float)($params['dias_mes'] ?? 30);
        
        // --- NUEVO: Capturar el factor de integración manual si viene del form ---
        if (!empty($params['factor_integracion'])) {
            $this->factor_integracion = (float)$params['factor_integracion'];
        }
    }

    public function calcularNetoABruto($neto_deseado)
    {
        // BLINDAJE EXTRA: Si el sistema intenta buscar un sueldo menor al mínimo, 
        // aborta la búsqueda y devuelve el mínimo directo.
        $neto_minimo_ley = $this->salario_minimo * $this->dias_mes;
        if ($neto_deseado <= ($neto_minimo_ley + 0.50)) {
            return $this->calcularDesgloseDesdeDiario($this->salario_minimo);
        }

        $bajo = $neto_deseado;
        $alto = $neto_deseado * 3; 
        for ($i = 0; $i < 100; $i++) {
            $diario = ($bajo + $alto) / 2 / $this->dias_mes;
            $res = $this->calcularDesgloseDesdeDiario($diario);
            
            if (abs($res['neto'] - $neto_deseado) < 0.01) {
                break;
            }
            
            if ($res['neto'] < $neto_deseado) {
                $bajo = $diario * $this->dias_mes;
            } else {
                $alto = $diario * $this->dias_mes;
            }
        }
        return $this->calcularDesgloseDesdeDiario($diario);
    }

    public function calcularDesgloseDesdeDiario($sd_input, $sdi_forzado = null)
    {
        // 1. TOPE LEGAL: Aseguramos que el SD no sea menor al mínimo
        $sd = max((float)$sd_input, (float)$this->salario_minimo);
        
        // 2. Factor de Integración configurable (ej. 1.0493)
        $factor = $this->factor_integracion;
        
        $dias = $this->dias_mes; 
        $bruto_mensual = $sd * $dias;
        
        // Sueldo Diario Nominal exacto
        $sueldo_diario_nominal = $bruto_mensual / $dias;

        // SBC oficial calculado como: Sueldo Diario Nominal * Factor de Integración
        if ($sdi_forzado !== null && $sdi_forzado > 0) {
            $sbc = min($sdi_forzado, $this->uma * 25);
        } else {
            $sbc = min($sueldo_diario_nominal * $factor, $this->uma * 25);
        }
        
        $sdi = $sueldo_diario_nominal * $factor;
        
        // 3. Cálculo de ISR Mensual
        $isr = 0;
        foreach ($this->tabla_isr as $r) {
            if ($bruto_mensual >= $r['inf'] && $bruto_mensual <= $r['sup']) {
                $isr = (($bruto_mensual - $r['inf']) * $r['pct']) + $r['cuota'];
                break;
            }
        }
        $subsidio = ($bruto_mensual <= 11492.66) ? 535.65 : 0;
        $isr_neto = max(0, $isr - $subsidio);
        
        // 4. Seguridad Social (IMSS Obrero, IMSS Patronal unificado e Infonavit usando el SBC real)
        $imss_obrero = $this->calcularIMSSObrero($sbc, $dias);
        $imss_patron_total = $this->calcularIMSSPatronTotal($sbc, $dias);
        $infonavit_total = ($sbc * 0.05) * $dias;

        // REGLA DE SALARIO MÍNIMO
        if ($sd <= ($this->salario_minimo + 0.50)) {
            $isr_neto = 0; 
            $imss_patron_total += $imss_obrero; 
            $imss_obrero = 0; 
        }

        $neto_mensual = $bruto_mensual - $isr_neto - $imss_obrero;

        // 5. Provisiones e ISN
        $prov_aguinaldo_mensual = ($sd * $this->dias_aguinaldo / 365) * $dias;
        $prov_pv_mensual = (($sd * $this->dias_vacaciones * ($this->prima_vacacional_pct / 100)) / 365) * $dias;
        $provisiones_totales = $prov_aguinaldo_mensual + $prov_pv_mensual;
        $isn_total = $bruto_mensual * $this->isn_rate;

        return [
            'salario_diario'        => $sd,
            'sueldo_diario_nominal' => $sueldo_diario_nominal, // <-- Campo nuevo expuesto
            'sdi'                   => $sdi,
            'sbc'                   => $sbc,                   // <-- Campo nuevo expuesto
            'bruto'                 => $bruto_mensual,
            'isr'                   => $isr_neto,
            'imss_obrero'           => $imss_obrero,
            'neto'                  => $neto_mensual,
            'imss_patron'           => $imss_patron_total,
            'infonavit'             => $infonavit_total,
            'isn'                   => $isn_total,
            'flujo_efectivo'        => $bruto_mensual + $imss_patron_total + $infonavit_total + $isn_total,
            'provisiones'           => $provisiones_totales,
            'gasto_real'            => $bruto_mensual + $imss_patron_total + $infonavit_total + $isn_total + $provisiones_totales
        ];
    }

    private function calcularIMSSObrero($sbc, $dias)
    {
        $exc = max(0, $sbc - (3 * $this->uma)) * $dias * 0.004;
        $pd = $sbc * $dias * 0.0025;
        $gm = $sbc * $dias * 0.00375;
        $iv = $sbc * $dias * 0.00625;
        $cv = $sbc * $dias * 0.01125;
        return $exc + $pd + $gm + $iv + $cv;
    }

    // --- NUEVA FUNCIÓN QUE SEPARA RCV E IMSS OPERATIVO ---
    // --- FUNCIÓN UNIFICADA IMSS PATRONAL (Alineada al Excel de Trébol Park) ---
    private function calcularIMSSPatronTotal($sbc, $dias)
    {
        // Cuota fija, excedente, prestaciones en dinero, gastos médicos, invalidez, guarderías y riesgo de trabajo + RCV unificado
        $cf = $this->uma * 0.204;
        $exc = max(0, $sbc - (3 * $this->uma)) * 0.011;
        $pd = $sbc * 0.007;
        $gm = $sbc * 0.0105;
        $iv = $sbc * 0.0175;
        $g = $sbc * 0.01;
        $r = $sbc * $this->prima_riesgo;
        
        // RCV integrado (Retiro 2% + Cesantía y Vejez según SBC)
        $ret = $sbc * 0.02;
        $cv_pct = $this->getPorcentajeCV($sbc);
        $rcv = $ret + ($sbc * $cv_pct);
        
        // Suma diaria total patronal IMSS + RCV
        $diario_patron = $cf + $exc + $pd + $gm + $iv + $g + $r + $rcv;
        
        return $diario_patron * $dias;
    }
    // -----------------------------------------------------

    private function getPorcentajeCV($sbc) {
        $f = $sbc / $this->uma;
        
        // Tabla de Cesantía y Vejez Patronal 2026 (Alineada a la imagen proporcionada)
        if ($sbc <= $this->salario_minimo) return 0.03150; // 3.150% (1.00 SM)
        if ($f <= 1.50) return 0.03676; // 3.676% (1.01 SM a 1.50 UMA)
        if ($f <= 2.00) return 0.04851; // 4.851% (1.51 a 2.00 UMA)
        if ($f <= 2.50) return 0.05560; // 5.56% (2.01 a 2.50 UMA)
        if ($f <= 3.00) return 0.06030; // 6.03% (2.51 a 3.00 UMA)
        if ($f <= 3.50) return 0.06360; // 6.36% (3.01 a 3.50 UMA)
        if ($f <= 4.00) return 0.06940; // 6.94% (3.51 a 4.00 UMA)
        
        // 4.01 UMA en adelante
        return 0.07510; // 7.51%
    }

    public function calcularEsquemaMixto($neto_total, $valor_nomina, $pct_comision, $tipo_servicio, $modo_nomina = 'pct')
    {
        $dias = $this->dias_mes;
        $bruto_minimo_ley = $this->salario_minimo * $dias;

        if ($modo_nomina === 'sdi') {
            $factor = 1 + ($this->dias_aguinaldo + ($this->dias_vacaciones * $this->prima_vacacional_pct)) / 365;
            $sdi_minimo_ley = $this->salario_minimo * $factor;
            
            $sdi = max((float)$valor_nomina, $sdi_minimo_ley);
            $sd = $sdi / $factor;
            
            $desglose_fiscal = $this->calcularDesgloseDesdeDiario($sd, $sdi);
        } else {
            $bruto_nomina_objetivo = $neto_total * ((float)$valor_nomina / 100);
            
            if ($bruto_nomina_objetivo < $bruto_minimo_ley) {
                $bruto_nomina_objetivo = $bruto_minimo_ley;
            }
            
            $sd = $bruto_nomina_objetivo / $dias;
            $desglose_fiscal = $this->calcularDesgloseDesdeDiario($sd);
        }

        // El complemento cubre la diferencia neta
        $complemento = max(0, $neto_total - $desglose_fiscal['neto']);
        
        // --- CORRECCIÓN CLAVE: Base de Comisión alineada al Excel ---
        // En el esquema especializado, la comisión (ej. 8%) opera sobre la base de percepciones (Neto + Exentas o la proporción equivalente)
        if ($tipo_servicio === 'proem') {
            $comision = $complemento * ((float)$pct_comision / 100);
        } else {
            // Alineado al Excel de Trébol Park: Comisión sobre la suma de base neta y complementaria / exenta
            $base_comision = $desglose_fiscal['neto'] + $complemento; 
            $comision = $base_comision * ((float)$pct_comision / 100);
        }

        // Carga social patronal exacta (IMSS Patronal + Infonavit + ISN)
        $carga_social_excel = $desglose_fiscal['imss_patron'] + $desglose_fiscal['infonavit'] + $desglose_fiscal['isn'];

        // --- CORRECCIÓN CLAVE: Costo Total Propuesta alineado al Excel ---
        // Costo Total = Sueldo Neto Fiscal + Percepciones Exentas (Complemento) + Carga Social + Comisión
        $total_propuesta = $desglose_fiscal['neto'] + $complemento + $carga_social_excel + $comision;

        return [
            'fiscal'          => $desglose_fiscal,
            'complemento'     => $complemento,
            'comision'        => $comision,
            'total_propuesta' => $total_propuesta,
            'tipo_servicio'   => $tipo_servicio
        ];
    }
}