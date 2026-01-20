<?php

namespace App\Filament\Dashboard\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                CheckboxList::make('roles')
                    ->label(__('Roles'))
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query
                            ->when(! auth()->user()->hasRole('super_admin'), fn ($q) => $q->where('name', '!=', 'super_admin'))
                    )
                    ->bulkToggleable()
                    ->getOptionLabelFromRecordUsing(fn ($record) => Str::headline($record->name))
                    ->columnSpanFull()
                    ->columns(3),

            ]);
    }
}
