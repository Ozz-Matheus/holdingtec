<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RoleEnum;
use App\Support\AppNotifier;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accesores / Métodos útiles
    |--------------------------------------------------------------------------
    */

    // Metodos para el Acceso.

    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return true;
        }

        if ($reason = $this->getAccessDenialReason()) {

            Auth::logout();
            AppNotifier::danger($reason['title'], $reason['message'], true);

            return false;

        }

        return true;
    }

    private function getAccessDenialReason(): ?array
    {
        return match (true) {

            tenant()?->is_active === false => [
                'title' => __('Workspace Deactivated'),
                'message' => __('This workspace is currently deactivated...'),
            ],

            ! $this->isActive() => [
                'title' => __('Account Deactivated'),
                'message' => __('Your account has been deactivated...'),
            ],

            default => null,

        };
    }
}
