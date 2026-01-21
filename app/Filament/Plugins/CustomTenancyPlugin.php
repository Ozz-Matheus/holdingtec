<?php

namespace App\Filament\Plugins;

use Filament\Panel;
use TomatoPHP\FilamentTenancy\FilamentTenancyPlugin;
use TomatoPHP\FilamentTenancy\Http\Middleware\RedirectIfInertiaMiddleware;

class CustomTenancyPlugin extends FilamentTenancyPlugin
{
    public function register(Panel $panel): void
    {
        $isActive = false;
        if (class_exists(\Nwidart\Modules\Module::class) && \Nwidart\Modules\Facades\Module::find('FilamentTenancy')?->isEnabled()) {
            $isActive = true;
        } else {
            $isActive = true;
        }

        if ($isActive) {
            $panel
                ->resources([
                ])
                ->middleware([
                    RedirectIfInertiaMiddleware::class,
                ])
                ->persistentMiddleware(['universal'])
                ->domains([
                    config('filament-tenancy.central_domain'),
                ]);
        }
    }

    public static function make(): static
    {
        return new static;
    }
}
