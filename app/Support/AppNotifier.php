<?php

namespace App\Support;

use Filament\Notifications\Notification;

class AppNotifier
{
    protected static function make(

        string $message,
        string $type = 'info',
        ?string $body = null,
        bool $persistent = false,
        int $duration = 5000,
        ?array $actions = null,
        ?string $icon = null

    ): Notification {

        $notification = Notification::make()
            ->title($message)
            ->duration($duration);

        if ($body) {
            $notification->body($body);
        }

        if ($persistent) {
            $notification->persistent();
        }

        if ($actions) {
            $notification->actions($actions);
        }

        if ($icon) {
            $notification->icon($icon);
        }

        $notification = match ($type) {
            'success' => $notification->success(),
            'danger' => $notification->danger(),
            'warning' => $notification->warning(),
            'info' => $notification->info(),
            default => $notification->info(),
        };

        $notification->send();

        return $notification;
    }

    public static function success(

        string $message,
        ?string $body = null,
        bool $persistent = false,
        ?array $actions = null,
        ?string $icon = null

    ): Notification {
        return self::make($message, 'success', $body, $persistent, 5000, $actions, $icon);
    }

    public static function danger(

        string $message,
        ?string $body = null,
        bool $persistent = false,
        ?array $actions = null,
        ?string $icon = null

    ): Notification {
        return self::make($message, 'danger', $body, $persistent, 5000, $actions, $icon);
    }

    public static function warning(

        string $message,
        ?string $body = null,
        bool $persistent = false,
        ?array $actions = null,
        ?string $icon = null

    ): Notification {
        return self::make($message, 'warning', $body, $persistent, 5000, $actions, $icon);
    }

    public static function info(

        string $message,
        ?string $body = null,
        bool $persistent = false,
        ?array $actions = null,
        ?string $icon = null

    ): Notification {
        return self::make($message, 'info', $body, $persistent, 5000, $actions, $icon);
    }

    public static function notify(

        string $message,
        string $type = 'info',
        ?string $body = null,
        bool $persistent = false,
        ?array $actions = null,
        ?string $icon = null

    ): Notification {
        return self::make($message, $type, $body, $persistent, 5000, $actions, $icon);
    }
}
