<?php

namespace App\Filament\Dashboard\Resources\Tenants\Pages;

use App\Filament\Dashboard\Resources\Tenants\TenantResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->visible(fn () => config('app.env') === 'local'),

            Action::make('import_db')
                ->label(__('Link Existing DB'))
                ->icon('heroicon-o-server')
                ->url(fn () => TenantResource::getUrl('import')),
        ];
    }
}
