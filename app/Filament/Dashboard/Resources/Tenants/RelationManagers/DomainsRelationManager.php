<?php

namespace App\Filament\Dashboard\Resources\Tenants\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DomainsRelationManager extends RelationManager
{
    protected static string $relationship = 'domains';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain')
                    ->required()
                    ->label(trans('filament-tenancy::messages.domains.columns.domain'))
                    ->regex('/^[a-z0-9]+(-[a-z0-9]+)*$/')
                    ->helperText('Solo letras minúsculas, números y guiones (ej: mi-empresa)')
                    ->prefix(request()->getScheme().'://')
                    ->suffix('.'.config('filament-tenancy.central_domain'))
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('domain')
            ->columns([
                TextColumn::make('domain')
                    ->label(trans('filament-tenancy::messages.domains.columns.domain')),
                TextColumn::make('full-domain')
                    ->label(trans('filament-tenancy::messages.domains.columns.full'))
                    ->getStateUsing(fn ($record) => Str::of($record->domain)->append('.')->append(config('filament-tenancy.central_domain'))),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
