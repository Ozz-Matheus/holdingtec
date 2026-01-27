<?php

namespace App\Observers;

use App\Models\Tenant;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class TenantObserver
{
    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Elimina las carpetas y enlaces simbólicos relacionados al tenant
     */
    public function deleting(Tenant $tenant): void
    {
        try {
            // Sanitiza el ID como hace TenantStorageInitializer
            $tenantId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $tenant->id);

            // Base del sufijo y rutas
            $suffixBase = config('tenancy.filesystem.suffix_base');
            $tenantStoragePath = storage_path();

            $publicDir = env('TENANCY_PUBLIC_DIR', 'public');
            $publicPath = base_path("{$publicDir}/{$suffixBase}{$tenantId}");

            Log::info("Url del Symlink : {$publicPath}");

            // Eliminar enlace simbólico en public/
            if ($this->filesystem->exists($publicPath)) {

                $this->filesystem->delete($publicPath);

                Log::info("Symlink eliminado: {$publicPath}");
            }

            // Eliminar carpetas específicas del tenant
            $tenantSpecificPath = "{$tenantStoragePath}/{$suffixBase}{$tenantId}";

            Log::info("Url de la Carpeta de tenant : {$tenantSpecificPath}");

            if ($this->filesystem->isDirectory($tenantSpecificPath)) {

                $this->filesystem->deleteDirectory($tenantSpecificPath);

                Log::info("Carpeta de tenant eliminada: {$tenantSpecificPath}");
            }

            Log::info("Limpieza completada para tenant {$tenant->id}");

        } catch (\Throwable $e) {

            Log::error("Error al eliminar carpetas del tenant {$tenant->id}: ".$e->getMessage());
        }
    }
}
