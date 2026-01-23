<?php

namespace App\Filament\Dashboard\Resources\Users\Pages;

use App\Enums\RoleEnum;
use App\Filament\Dashboard\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(function () {
                    return ! $this->record->hasRole(RoleEnum::SUPER_ADMIN->value);
                }),
            RestoreAction::make(),
            ForceDeleteAction::make()
                ->visible(fn ($record): bool => auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value) && ! $this->record->hasRole(RoleEnum::SUPER_ADMIN->value)),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
