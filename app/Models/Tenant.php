<?php

namespace App\Models;

use TomatoPHP\FilamentTenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $table = 'tenants';

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
