<?php

namespace App\Filament\Dashboard\Resources\Tenants\Pages;

use App\Filament\Dashboard\Resources\Tenants\TenantResource;
use App\Services\TenantCreatorService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        // Dejamos que el plugin cree el tenant, database y lance los seeds.
        $record = parent::handleRecordCreation(collect($data)->except('domain')->toArray());
        $record->domains()->create(['domain' => $data['domain']]);

        return $record;
    }

    protected function afterCreate(): void
    {
        // 1. Agregamos el owner
        DB::table('tenants')
            ->where('id', $this->record->id)
            ->update(['user_id' => auth()->id()]);

        // 2. Configurar el Super Admin solamente.
        TenantCreatorService::setup(
            $this->record,
            $this->form->getRawState()['password'],
            runMigrations: false,
            runSeeds: false
        );
    }
}
