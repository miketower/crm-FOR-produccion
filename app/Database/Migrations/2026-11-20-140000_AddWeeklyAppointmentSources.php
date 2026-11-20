<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWeeklyAppointmentSources extends Migration
{
    private const COLUMNAS = ['citas_bni', 'citas_qro', 'citas_mkt'];

    public function up()
    {
        $faltantes = array_diff(self::COLUMNAS, $this->db->getFieldNames('capturas_comerciales'));
        if ($faltantes !== []) {
            $this->forge->addColumn('capturas_comerciales', array_fill_keys($faltantes, [
                'type' => 'INT',
                'default' => 0,
            ]));
        }
    }

    public function down()
    {
        $this->forge->dropColumn('capturas_comerciales', self::COLUMNAS);
    }
}
