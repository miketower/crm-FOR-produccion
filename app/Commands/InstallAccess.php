<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/** Upgrade an existing schema dump without replaying its historical migrations. */
class InstallAccess extends BaseCommand
{
    protected $group = 'CRM';
    protected $name = 'access:install';
    protected $description = 'Instala roles y permisos sobre una base existente, sin repetir migraciones anteriores.';

    public function run(array $params)
    {
        $runner = service('migrations');
        $runner->ensureTable();
        foreach ($runner->getHistory() as $history) {
            if (str_ends_with($history->class, 'CreateAccessControl') && $history->namespace === 'App') {
                CLI::write('Roles y permisos ya están instalados.', 'green');
                return EXIT_SUCCESS;
            }
        }
        if (!$runner->force(APPPATH . 'Database/Migrations/2026-10-26-180000_CreateAccessControl.php', 'App')) {
            CLI::error('No fue posible instalar los accesos.');
            return EXIT_ERROR;
        }
        CLI::write('Roles y permisos instalados. Administra los accesos desde Equipo / Usuarios.', 'green');
        return EXIT_SUCCESS;
    }
}
