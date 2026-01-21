<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ProjectCleanCommand extends Command
{
    protected $signature = 'project:clean';

    protected $description = 'Limpia cachés, vistas, rutas y optimiza Filament.';

    public function handle()
    {
        // 1. Bloqueo de seguridad para Producción
        if (App::isProduction()) {

            $this->error('¡ALERTA! Por seguridad, evita correr limpiezas agresivas automatizadas en producción.');

            return Command::FAILURE;
        }

        $this->info('Iniciando limpieza profunda...');

        // 2. Ejecución de comandos

        // limpia todas las cachés del framework
        $this->call('optimize:clear');

        // Limpia lo específico de Filament
        $this->call('filament:optimize-clear');

        // Al final regeneramos la caché de config
        $this->call('config:cache');

        $this->info('¡Sistema limpio y optimizado!');

        return Command::SUCCESS;
    }
}
