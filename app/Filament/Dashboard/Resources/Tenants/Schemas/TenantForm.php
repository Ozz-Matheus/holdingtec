<?php

namespace App\Filament\Dashboard\Resources\Tenants\Schemas;

use App\Filament\Dashboard\Resources\Tenants\Pages;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        // Obtenemos las bases de datos disponibles excluyendo las del sistema
        $centralDb = config('database.connections.mysql.database');
        $systemSchemas = ['information_schema', 'performance_schema', 'mysql', 'sys'];

        $databases = collect(\DB::select('SHOW DATABASES'))
            ->pluck('Database')
            ->reject(fn ($db) => in_array($db, $systemSchemas) || $db === $centralDb)
            ->values();

        return $schema
            ->components([

                Section::make()
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->label(trans('filament-tenancy::messages.columns.name'))
                            ->required()
                            ->live(onBlur: true)
                            ->unique(table: 'tenants', ignoreRecord: true)->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state, $livewire) {
                                $set('domain', \Str::of($state)->slug()->toString());
                                if ($livewire instanceof Pages\CreateTenant) {
                                    $set('id', \Str::of($state)->slug('_')->toString());
                                }
                            }),

                        TextInput::make('id')
                            ->label(trans('filament-tenancy::messages.columns.unique_id'))
                            ->required()
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateTenant)
                            ->unique(table: 'tenants', ignoreRecord: true),

                        Select::make('id')
                            ->label(__('Existing Database (Unique ID)'))
                            ->options($databases->mapWithKeys(fn ($db) => [$db => $db]))
                            ->searchable()
                            ->required()
                            ->unique(table: 'tenants', ignoreRecord: true)
                            ->visible(fn ($livewire) => $livewire instanceof Pages\ImportDbTenant),

                        TextInput::make('domain')
                            ->columnSpanFull()
                            ->label(trans('filament-tenancy::messages.columns.domain'))
                            ->required()
                            ->visible(fn ($context) => $context === 'create')
                            ->unique(table: 'domains', ignoreRecord: true)
                            ->prefix(request()->getScheme().'://')
                            ->suffix('.'.request()->getHost()),

                        TextInput::make('email')
                            ->label(trans('filament-tenancy::messages.columns.email'))
                            ->required()
                            ->email(),

                        TextInput::make('phone')
                            ->label(trans('filament-tenancy::messages.columns.phone'))
                            ->tel(),

                        TextInput::make('password')
                            ->label(trans('filament-tenancy::messages.columns.password'))
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),

                        TextInput::make('passwordConfirmation')
                            ->label(trans('filament-tenancy::messages.columns.passwordConfirmation'))
                            ->password()
                            ->revealable()
                            ->dehydrated(false),

                        Toggle::make('is_active')
                            ->label(trans('filament-tenancy::messages.columns.is_active'))
                            ->default(true),

                    ]),

            ])->columns(1);
    }
}
