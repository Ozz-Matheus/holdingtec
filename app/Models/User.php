<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        // 1️⃣ Super Admin
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // 2️⃣ Evaluamos condiciones de bloqueo
        // match(true) buscará la primera condición que se cumpla
        $failure = match (true) {
            tenant()?->is_active === false => [
                'Workspace Deactivated',
                'This workspace is currently deactivated. Contact the administrator.',
            ],
            ! $this->isActive() => [
                'Account Deactivated',
                'Your account has been deactivated. Contact the administrator.',
            ],
            default => null, // Si todo está bien
        };

        // 3️⃣ Ejecutamos el cierre de sesión una sola vez
        if ($failure) {

            Auth::logout();

            AppNotifier::danger($failure[0], $failure[1], true);

            return false;
        }

        return true;
    }
}
