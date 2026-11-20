<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InstallWeeklyAppointmentSources extends BaseCommand
{
    protected $group = 'CRM';
    protected $name = 'weekly:install';
    protected $description = 'Agrega BNI, QRO y MKT al desglose de citas de Carga Semanal.';

    public function run(array $params)
    {
        $runner = service('migrations');
        $runner->ensureTable();
        foreach ($runner->getHistory() as $history) {
            if ($history->namespace === 'App' && str_ends_with($history->class, 'AddWeeklyAppointmentSources')) {
                CLI::write('Los nuevos orígenes de citas ya están instalados.', 'green');
                return EXIT_SUCCESS;
            }
        }

        if (!$runner->force(APPPATH . 'Database/Migrations/2026-11-20-140000_AddWeeklyAppointmentSources.php', 'App')) {
            CLI::error('No fue posible instalar los nuevos orígenes de citas.');
            return EXIT_ERROR;
        }

        CLI::write('Orígenes de citas BNI, QRO y MKT instalados.', 'green');
        return EXIT_SUCCESS;
    }
}
