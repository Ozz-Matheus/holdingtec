<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\RoleEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->maxLength(255)
                            ->nullable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context) => $context === 'create')
                            ->helperText(
                                fn (string $context) => $context === 'edit'
                                    ? __('user.leave_empty_to_keep_password')
                                    : null
                            ),
                        CheckboxList::make('roles')
                            ->label(__('Roles'))
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query
                                    ->when(! auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value), fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))
                            )
                            ->bulkToggleable()
                            ->getOptionLabelFromRecordUsing(fn ($record) => Str::headline($record->name))
                            ->disabled(fn ($record) => auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value) && $record?->is(auth()->user()))
                            ->columnSpanFull()
                            ->columns(3),
                        Toggle::make('active')
                            ->label(__('Active'))
                            ->helperText(__('user.toggle_user_access'))
                            ->required()
                            ->default(true),

                    ]),

            ])->columns(1);
    }
}
