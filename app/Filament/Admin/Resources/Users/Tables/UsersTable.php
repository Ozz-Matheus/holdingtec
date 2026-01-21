<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->copyable()
                    ->copyMessage(__('Email copied to clipboard'))
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->formatStateUsing(fn ($state) => Str::headline($state))
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'indigo',
                        'admin' => 'success',
                        'panel_user' => 'primary',
                        default => 'gray',
                    }),
                ToggleColumn::make('active')
                    ->sortable()
                    ->label(trans('filament-tenancy::messages.columns.is_active')),
                TextColumn::make('email_verified_at')
                    ->label(__('user.email_verified_on'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label(trans('filament-tenancy::messages.columns.is_active')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
