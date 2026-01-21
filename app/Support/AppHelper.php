<?php

namespace App\Support;

use Filament\Facades\Filament;

class AppHelper
{
    public static function getHomeUrl(): string
    {
        $currentHost = request()->getHost();
        $centralDomains = config('tenancy.central_domains', []);

        if (in_array($currentHost, $centralDomains)) {
            try {
                return Filament::getPanel('dashboard')->getUrl();
            } catch (\Throwable $e) {
                return url('/dashboard');
            }
        } else {
            try {
                return Filament::getPanel('admin')->getUrl();
            } catch (\Throwable $e) {
                return url('/admin');
            }
        }
    }
}
