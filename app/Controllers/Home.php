<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function forzarMigracion()
{
    $migrations = \Config\Services::migrations();

    try {
        // Ejecuta todas las migraciones pendientes en la base de datos
        $migrations->latest();
        return "¡Gema Actualizada! Todas las migraciones se ejecutaron con éxito en producción.";
    } catch (\Throwable $e) {
        return "Error al migrar: " . $e->getMessage();
    }
}
}
