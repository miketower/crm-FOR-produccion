<?php

use App\Libraries\Authorization;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;
use Config\Services;

final class CotizadorTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private $connection;
    private array $tables = ['propuestas_for', 'detalle_especializado', 'detalle_proem', 'market_benchmark', 'perfiles_plantilla'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = Database::connect('tests');
        // Only quotation fields are needed; permissions and layout queries are unrelated.
        $access = $this->createMock(Authorization::class);
        $access->method('user')->willReturn(['id' => 1, 'role' => 'admin']);
        $access->method('can')->willReturnCallback(static fn(string $permission): bool => str_starts_with($permission, 'cotizador.'));
        $access->method('menu')->willReturn([]);
        $access->method('landingPage')->willReturn('cotizador');
        Services::injectMock('authorization', $access);

        $this->createTable('propuestas_for',
            'nombre_cliente,fecha_propuesta,solicitante,cargo_solicitante,impresion_diagnostica,req_intervencion,motivo_intervencion,plan_requerido,detalle_otros,periodo_pago',
            'diag_360_inicial,nivel_riesgo,comision_pct,actual_bruto,actual_nomina,actual_excedente,actual_carga,actual_comision,actual_total');
        $this->createTable('detalle_especializado', '',
            'propuesta_id,base_100,neto_100,carga_100,sueldos_for,neto_for,excedente,carga_for,costo_op_100,comision_monto,total_for,diferencia');
        $this->createTable('detalle_proem', '',
            'propuesta_id,base_100,neto_100,carga_100,comision_100,comision_100_afecta,bruto_proem,neto_proem,carga_proem,comision_empleado_proem,costo_op_100,excedente,comision_monto,total_proem,ahorro_mensual');
        $this->createTable('market_benchmark', 'puesto', 'propuesta_id,percentile_25,mean,median,percentile_75');
        $this->createTable('perfiles_plantilla', 'tipo_plan,cargo', 'propuesta_id,cantidad');
    }

    private function createTable(string $table, string $textColumns, string $numberColumns): void
    {
        $forge = Database::forge('tests');
        $fields = ['id' => ['type' => 'INTEGER', 'auto_increment' => true]];
        if ($table === 'propuestas_for') {
            $fields['fecha_creacion'] = ['type' => 'TEXT', 'default' => '2026-10-26 12:00:00'];
        }
        foreach (array_filter(explode(',', $textColumns)) as $column) {
            $fields[$column] = ['type' => 'TEXT', 'null' => true];
        }
        foreach (explode(',', $numberColumns) as $column) {
            $fields[$column] = ['type' => 'NUMERIC', 'default' => 0];
        }
        $forge->addField($fields);
        $forge->addKey('id', true);
        $forge->createTable($table);
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            Database::forge('tests')->dropTable($table, true);
        }
        $_SESSION = [];
        parent::tearDown();
    }

    public static function plans(): array
    {
        return [['Servicio Especializado'], ['Mixto']];
    }

    /** @dataProvider plans */
    public function testGrossNetAndSurplusSurviveCreateEditAndSave(string $plan): void
    {
        $payload = [
            'nombre_cliente' => 'Cotizador regression', 'fecha_propuesta' => '2026-10-26',
            'plan_integral' => $plan, 'comision_porcentaje' => 5, 'periodo_pago' => 'Mensual',
            'esp_base_100' => 30000, 'esp_neto_100' => 25000, 'esp_carga_100' => 9000,
            'esp_bruto_for' => 20000.25, 'esp_neto_for' => 17000.50, 'esp_excedente' => 7999.50,
            'esp_carga_for' => 6000, 'esp_comision_monto' => 1250,
            'esp_total_100' => 39000, 'esp_total_for' => 35249.75, 'esp_diferencia' => 3750.25,
        ];
        $this->withSession(['id' => 1, 'name' => 'Tester', 'isLoggedIn' => true]);
        $response = $this->withBodyFormat('json')->post('cotizador/guardar', $payload);
        $response->assertJSONFragment(['status' => 'success']);
        $id = json_decode($response->getJSON(), true)['id'];
        $this->assertSame(1, $this->connection->table('propuestas_for')->countAllResults());
        $this->assertSpecializedValues($id, 20000.25, 17000.50, 7999.50, 35249.75, 3750.25);

        $response = $this->get('cotizador/editar/' . $id);
        $response->assertOK();
        $html = $response->getBody();
        $this->assertInputValue($html, 'esp_bruto_for', '20000.25');
        $this->assertInputValue($html, 'esp_neto_for', '17000.5');
        $this->assertStringContainsString('id="esp_comision_monto"', $html);
        $this->assertStringContainsString('id="esp_excedente_val"', $html);
        $this->assertStringNotContainsString('id="esp_sueldos_for"', $html);

        // Saving an edit uses the same JSON field as creating the quotation.
        $payload['esp_bruto_for'] = 21000.75;
        $payload['esp_neto_for'] = 18000.25;
        $payload['esp_excedente'] = 6999.75;
        $payload['esp_total_for'] = 35250.50;
        $payload['esp_diferencia'] = 3749.50;
        $response = $this->withBodyFormat('json')->post('cotizador/actualizar/' . $id, $payload);
        $response->assertJSONFragment(['status' => 'success']);
        $this->assertSpecializedValues($id, 21000.75, 18000.25, 6999.75, 35250.50, 3749.50);
        $this->assertSame(1, $this->connection->table('detalle_especializado')->countAllResults());

        $response = $this->get('cotizador/editar/' . $id);
        $response->assertOK();
        $this->assertInputValue($response->getBody(), 'esp_bruto_for', '21000.75');
        $this->assertInputValue($response->getBody(), 'esp_neto_for', '18000.25');
        $response = $this->get('cotizador/ver/' . $id);
        $response->assertOK();
        $this->assertStringContainsString('$35,250.50', $response->getBody());
    }

    public function testNewFormUsesTheSameSpecializedInputsAsEdit(): void
    {
        $this->withSession(['id' => 1, 'name' => 'Tester', 'isLoggedIn' => true]);
        $response = $this->get('cotizador/nuevo');
        $response->assertOK();
        foreach (['esp_bruto_for', 'esp_neto_for', 'esp_carga_for', 'esp_comision_monto', 'esp_excedente_val'] as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $response->getBody());
        }
    }

    public static function proemPlans(): array
    {
        return [['Proem', false], ['Proem', true], ['Mixto', false], ['Mixto', true]];
    }

    /** @dataProvider proemPlans */
    public function testProemGrossAndCommissionsSurviveCreateEditAndSave(string $plan, bool $withEmployeeCommission): void
    {
        $employeeCommission = $withEmployeeCommission ? 1000 : 0;
        $payload = [
            'nombre_cliente' => 'PROEM Excel', 'fecha_propuesta' => '2026-10-28',
            'plan_integral' => $plan, 'comision_porcentaje' => 8, 'periodo_pago' => 'Mensual',
            'base_100' => 466568, 'neto_100' => 382095, 'carga_100' => 127156.75,
            'bruto_proem' => 234389, 'neto_proem' => 207784, 'carga_proem' => 68314.14,
            'comision_100' => $employeeCommission, 'comision_100_afecta' => (int) $withEmployeeCommission,
            'comision_empleado_proem' => $employeeCommission,
            'excedente' => 174311 + $employeeCommission,
            'comision_for_monto' => $withEmployeeCommission ? 14024.88 : 13944.88,
            'total_100' => $withEmployeeCommission ? 594974.75 : 593724.75,
            'total_proem' => $withEmployeeCommission ? 492039.02 : 490959.02,
            'ahorro_mensual' => $withEmployeeCommission ? 102935.73 : 102765.73,
        ];
        $this->withSession(['id' => 1, 'name' => 'Tester', 'isLoggedIn' => true]);
        $response = $this->withBodyFormat('json')->post('cotizador/guardar', $payload);
        $response->assertJSONFragment(['status' => 'success']);
        $id = json_decode($response->getJSON(), true)['id'];

        $response = $this->get('cotizador/editar/' . $id);
        $response->assertOK();
        $html = $response->getBody();
        $this->assertInputValue($html, 'bruto_proem', '234389');
        $this->assertInputValue($html, 'neto_proem', '207784');
        $this->assertInputValue($html, 'comision_100', (string) $employeeCommission);
        $this->assertInputValue($html, 'comision_empleado_proem', (string) $employeeCommission);
        $this->assertStringContainsString('id="comision_for_monto"', $html);
        preg_match('/<input\b[^>]*id="comision_100_afecta_carga"[^>]*>/', $html, $checkbox);
        $this->assertNotEmpty($checkbox);
        $this->assertSame($withEmployeeCommission, strpos($checkbox[0], 'checked') !== false);

        $payload['bruto_proem'] += 100;
        $payload['total_proem'] += 100;
        $payload['ahorro_mensual'] -= 100;
        $response = $this->withBodyFormat('json')->post('cotizador/actualizar/' . $id, $payload);
        $response->assertJSONFragment(['status' => 'success']);
        $row = $this->connection->table('detalle_proem')->where('propuesta_id', $id)->get()->getRowArray();
        $this->assertSame(1, $this->connection->table('detalle_proem')->countAllResults());
        foreach (['bruto_proem', 'neto_proem', 'carga_proem', 'comision_100', 'comision_100_afecta',
            'comision_empleado_proem', 'excedente', 'total_proem', 'ahorro_mensual'] as $column) {
            $this->assertEquals($payload[$column], $row[$column], $column);
        }
        $this->assertEquals($payload['comision_for_monto'], $row['comision_monto']);
        $this->assertEquals($payload['total_100'], $row['costo_op_100']);
        $response = $this->get('cotizador/ver/' . $id);
        $response->assertOK();
        $this->assertStringContainsString('$' . number_format($payload['total_proem'], 2), $response->getBody());
    }

    private function assertInputValue(string $html, string $id, string $value): void
    {
        $this->assertMatchesRegularExpression('/<input\b[^>]*id="' . $id . '"[^>]*value="' . preg_quote($value, '/') . '"/', $html);
    }

    private function assertSpecializedValues(int $id, float $gross, float $net, float $surplus, float $total, float $savings): void
    {
        $row = $this->connection->table('detalle_especializado')->where('propuesta_id', $id)->get()->getRowArray();
        $this->assertNotNull($row);
        foreach (['sueldos_for' => $gross, 'neto_for' => $net, 'excedente' => $surplus,
            'carga_for' => 6000, 'comision_monto' => 1250, 'total_for' => $total, 'diferencia' => $savings] as $column => $expected) {
            $this->assertEquals($expected, $row[$column], $column);
        }
    }
}
