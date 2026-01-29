<?php

namespace App\Filament\Dashboard\Resources\Tenants\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('domains'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('Identifier'))
                    ->copyable()
                    ->copyMessage(__('Copied to clipboard'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(trans('filament-tenancy::messages.columns.name'))
                    ->searchable()
                    ->description(function ($record) {
                        $domain = $record->domains->first()?->domain;

                        return request()->getScheme().'://'.$domain.'.'.config('filament-tenancy.central_domain').'/admin';
                    }),
                ToggleColumn::make('is_active')
                    ->sortable()
                    ->label(trans('filament-tenancy::messages.columns.is_active')),
                TextColumn::make('owner.name')
                    ->label(__('Created By'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(trans('filament-tenancy::messages.columns.is_active')),
                SelectFilter::make('owner_id')
                    ->label(__('Created By'))
                    ->relationship('owner', 'name', fn ($query) => $query->orderBy('name'))
                    ->searchable()
                    ->preload(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('view')
                    ->label(trans('filament-tenancy::messages.actions.view'))
                    ->tooltip(trans('filament-tenancy::messages.actions.view'))
                    ->iconButton()
                    ->icon('heroicon-s-link')
                    ->url(fn ($record) => request()->getScheme().'://'.$record->domains()->first()?->domain.'.'.config('filament-tenancy.central_domain').'/'.filament('filament-tenancy')->panel)
                    ->openUrlInNewTab(),
                Action::make('login')
                    ->label(trans('filament-tenancy::messages.actions.login'))
                    ->tooltip(trans('filament-tenancy::messages.actions.login'))
                    ->visible(filament('filament-tenancy')->allowImpersonate)
                    ->requiresConfirmation()
                    ->color('warning')
                    ->iconButton()
                    ->icon('heroicon-s-arrow-left-on-rectangle')
                    ->action(function ($record) {
                        $token = tenancy()->impersonate($record, auth()->id(), '/admin', 'web');

                        return redirect()->to(request()->getScheme().'://'.$record->domains[0]->domain.'.'.config('filament-tenancy.central_domain').'/login/url?token='.$token->token.'&email='.urlencode($record->email));
                    }),
                EditAction::make()
                    ->label(trans('filament-tenancy::messages.actions.edit'))
                    ->tooltip(trans('filament-tenancy::messages.actions.edit'))
                    ->iconButton(),
                DeleteAction::make()
                    ->label(trans('filament-tenancy::messages.actions.delete'))
                    ->tooltip(trans('filament-tenancy::messages.actions.delete'))
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
