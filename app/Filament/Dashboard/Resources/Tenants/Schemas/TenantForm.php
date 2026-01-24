<?php

namespace App\Filament\Dashboard\Resources\Tenants\Schemas;

use App\Filament\Dashboard\Resources\Tenants\Pages;
use App\Rules\ExistingTenantDatabase;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([

                        Toggle::make('link_existing_db')
                            ->label(__('Link Existing Database'))
                            ->default(true)
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('id', null))
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateTenant && App::isLocal()),

                        TextInput::make('name')
                            ->label(trans('filament-tenancy::messages.columns.name'))
                            ->required()
                            ->live(onBlur: true)
                            ->unique(table: 'tenants', ignoreRecord: true)
                            ->afterStateUpdated(function ($set, $state, $livewire, $get) {
                                $set('domain', Str::of($state)->slug()->limit(63)->toString());
                                if ($livewire instanceof Pages\CreateTenant && ! $get('link_existing_db')) {
                                    $set('id', Str::of($state)->slug('_')->toString());
                                }
                            }),

                        TextInput::make('id')
                            ->label(trans('filament-tenancy::messages.columns.unique_id'))
                            ->required()
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateTenant)
                            ->unique(table: 'tenants', ignoreRecord: true)
                            ->rules([
                                fn ($get) => $get('link_existing_db')
                                    ? new ExistingTenantDatabase
                                    : null,
                            ]),

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
