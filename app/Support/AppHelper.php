<?php

namespace App\Support;

use Filament\Facades\Filament;

class AppHelper
{
    public static function getHomeUrl(): string
    {
        // 1. Centralizamos la lógica de decisión
        $panelId = in_array(request()->getHost(), config('tenancy.central_domains', []))
            ? 'dashboard'
            : 'admin';

        // 2. Intentamos obtener la ruta oficial del panel
        try {
            return Filament::getPanel($panelId)?->getUrl() ?? url("/{$panelId}");
        } catch (\Throwable) {
            // 3. Fallback silencioso: Si el panel no carga, devolvemos la URL manual
            // Esto evita romper la navegación del usuario.
            return url("/{$panelId}");
        }

    }
}
