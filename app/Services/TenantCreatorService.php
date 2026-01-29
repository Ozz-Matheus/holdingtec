<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Mail\TenantCredentialsMail;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Stancl\Tenancy\Facades\Tenancy;

class TenantCreatorService
{
    /**
     * Flujo completo para crear o importar un Tenant de forma atómica y segura.
     */
    public static function create(array $data): Tenant
    {
        // 1. CREACIÓN DEL REGISTRO (Dentro de Transacción)
        // Esto es rápido y solo bloquea la BD central el tiempo mínimo necesario.
        $tenant = DB::transaction(function () use ($data) {

            // Instanciamos sin guardar todavía
            $tenant = new Tenant;

            if (isset($data['id'])) {
                $tenant->id = $data['id'];
            }

            // Llenamos datos básicos
            $tenant->fill(collect($data)->except(['domain', 'password_confirmation'])->toArray());

            // Guardamos silenciosamente
            // Esto evita conflictos con el paquete Tomato tenacy
            $tenant->saveQuietly();

            // Asignamos metadatos directamente
            // Esto evita conflictos con el paquete Tomato tenacy
            DB::table('tenants')->where('id', $tenant->id)->update([
                'user_id' => auth()->id(),
                'data' => json_encode([
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                    'tenancy_db_name' => $tenant->id,
                ]),
            ]);

            // Creamos el sub dominio
            $tenant->domains()->create(['domain' => $data['domain']]);

            return $tenant;

        });

        // 2. Preparamos la base de datos del Tenant (Admin, Migraciones, etc.)
        try {
            self::setup(
                $tenant,
                runMigrations: true,
                runSeeds: true
            );
        } catch (\Throwable $e) {
            // Rollback manual si falla el setup
            $tenant->delete();
            Log::critical("Fallo setup Tenant {$tenant->id}: {$e->getMessage()}");
            throw $e;
        }

        return $tenant;
    }

    /**
     * Configuración del entorno del Tenant
     */
    public static function setup(

        Tenant $tenant,
        bool $runMigrations = true,
        bool $runSeeds = true

    ): void {
        try {
            // ---  Inicializa el contexto del tenant ---
            Tenancy::initialize($tenant);

            // --- A. MIGRACIONES ---
            if ($runMigrations) {

                $migrator = app('migrator');
                $migrationPath = database_path('migrations/tenant');

                // Ahora verificamos si existe la db del tenant
                if (! $migrator->getRepository()->repositoryExists()) {
                    $migrator->getRepository()->createRepository();
                }

                // Ejecutar las migraciones
                $migrator->run([$migrationPath]);
            }

            // --- B. SEEDERS ---
            if ($runSeeds && class_exists(DatabaseSeeder::class)) {
                // Silenciamos errores de seeders no críticos para no abortar el proceso
                try {
                    app(DatabaseSeeder::class)->__invoke();
                } catch (\Throwable $e) {
                    Log::error("Seeder error en tenant {$tenant->id}: ".$e->getMessage());
                }
            }

            // --- C. CREACIÓN DE USUARIOS ---
            self::createSuperAdmin($tenant);
            self::createTenantAdmin($tenant);

        } catch (\Throwable $e) {

            // Loguear error crítico y relanzar para rollback de transacción
            Log::critical("Fallo crítico en setup de Tenant {$tenant->id}: {$e->getMessage()}");

            throw $e; // Re-lanzamos para que Filament sepa que falló
        } finally {
            // Limpia el contexto del tenant
            Tenancy::end();
        }
    }

    /**
     * Crea o actualiza el Super Admin del sistema dentro del Tenant.
     */
    protected static function createSuperAdmin(Tenant $tenant): void
    {
        $superAdminMail = config('holdingtec.super_admin.email', 'admin@holdingtec.app');
        $envPassword = config('holdingtec.super_admin.password');

        $missingEnv = empty($envPassword);
        $superAdminPass = $missingEnv ? Str::random(16) : $envPassword;

        $superAdmin = User::updateOrCreate(
            ['email' => $superAdminMail],
            [
                'name' => 'HoldingTec SuperAdmin',
                'password' => bcrypt($superAdminPass),
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

        // Token de reset
        $token = Password::broker()->createToken($superAdmin);

        // 1. Obtenemos el subdominio guardado (ej: 'ocaso')
        $subdomain = $tenant->domains->first()->domain;

        // 2. Obtenemos el dominio central de la config (ej: 'holdingtec.test')
        // Usamos la misma config que usas en TenantsTable.php
        $centralDomain = config('filament-tenancy.central_domain') ?? request()->getHost();

        // 3. Construimos el FQDN (Fully Qualified Domain Name) -> ocaso.holdingtec.test
        $fullDomain = $subdomain.'.'.$centralDomain;

        $resetUrl = tenant_route($fullDomain, 'password.reset', [
            'token' => $token,
            'email' => $superAdminMail,
        ]);

        if (App::isProduction() || $missingEnv) {
            try {
                Mail::to($superAdminMail)->send(
                    new TenantCredentialsMail($tenant->name, $resetUrl)
                );
            } catch (\Throwable $e) {
                Log::warning("No se pudo enviar correo al SuperAdmin: {$e->getMessage()}");
            }
        }
    }

    /**
     * Crea el usuario administrador propio del Tenant.
     */
    protected static function createTenantAdmin(Tenant $tenant): void
    {

        $admin = User::updateOrCreate(
            ['email' => $tenant->email],
            [
                'name' => $tenant->name,
                'password' => $tenant->password, // Asumiendo que ya viene hasheado del formulario
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol basado en quien lo crea, para mantener jerarquía
        $roleToAssign = auth()->user()?->hasRole(RoleEnum::SUPER_ADMIN->value)
            ? RoleEnum::SUPER_ADMIN->value
            : RoleEnum::ADMIN->value;

        $admin->assignRole($roleToAssign);
    }
}
