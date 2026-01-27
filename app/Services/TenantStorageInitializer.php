<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class TenantStorageInitializer
{
    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function ensureStorageStructure(string $tenantId): void
    {
        // Sanitiza el ID del tenant (solo letras, números, guiones y guion bajo)
        $tenantId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $tenantId);

        $suffixBase = config('tenancy.filesystem.suffix_base');
        $tenantStoragePath = storage_path();

        $publicDir = env('TENANCY_PUBLIC_DIR', 'public');
        $publicPath = base_path("{$publicDir}/{$suffixBase}{$tenantId}");

        // Asegura directorios necesarios
        $this->filesystem->ensureDirectoryExists("{$tenantStoragePath}/app/private", 0755, true);
        $this->filesystem->ensureDirectoryExists("{$tenantStoragePath}/app/public", 0755, true);
        $this->filesystem->ensureDirectoryExists("{$tenantStoragePath}/framework/cache", 0755, true);

        // Crea enlace simbólico si no existe
        if (! $this->filesystem->exists($publicPath)) {
            try {
                $this->filesystem->link(
                    "{$tenantStoragePath}/app/public",
                    $publicPath
                );
            } catch (\Throwable $e) {
                Log::warning("No se pudo crear enlace simbólico para tenant {$tenantId}: {$e->getMessage()}");
            }
        }
    }
}
