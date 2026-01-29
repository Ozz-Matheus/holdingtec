<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoleEnum: string implements HasLabel
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case PANEL_USER = 'panel_user';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUPER_ADMIN => __('Super Admin'),
            self::ADMIN => __('Admin'),
            self::PANEL_USER => __('Panel User'),
        };
    }

    public static function match(string $value): string
    {
        return self::tryFrom($value)?->getLabel() ?? Str::headline($value);
    }
}
