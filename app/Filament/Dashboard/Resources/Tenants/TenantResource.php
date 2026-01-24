<?php

namespace App\Filament\Dashboard\Resources\Tenants;

use App\Filament\Dashboard\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Dashboard\Resources\Tenants\Pages\EditTenant;
use App\Filament\Dashboard\Resources\Tenants\Pages\ListTenants;
use App\Filament\Dashboard\Resources\Tenants\Pages\ViewTenant;
use App\Filament\Dashboard\Resources\Tenants\RelationManagers\DomainsRelationManager;
use App\Filament\Dashboard\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Dashboard\Resources\Tenants\Tables\TenantsTable;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::GlobeAlt;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('Global Management');
    }

    public static function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DomainsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'view' => ViewTenant::route('/{record}'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
