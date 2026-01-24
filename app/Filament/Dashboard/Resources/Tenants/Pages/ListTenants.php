<?php

namespace App\Filament\Dashboard\Resources\Tenants\Pages;

use App\Filament\Dashboard\Resources\Tenants\TenantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make(),
        ];
    }
}
