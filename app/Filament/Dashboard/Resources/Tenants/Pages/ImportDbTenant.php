<?php

namespace App\Filament\Dashboard\Resources\Tenants\Pages;

use App\Filament\Dashboard\Resources\Tenants\TenantResource;
use App\Models\Tenant;
use App\Services\TenantCreatorService;
use App\Support\AppNotifier;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class ImportDbTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validación extra de seguridad
        if (Tenant::where('id', $data['id'])->exists()) {
            AppNotifier::error('Error', "La base de datos [{$data['id']}] ya existe.", true);
            abort(403, 'Database already assigned');
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return TenantCreatorService::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
