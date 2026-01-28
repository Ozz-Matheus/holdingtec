<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Mail\TenantCredentialsMail;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stancl\Tenancy\Facades\Tenancy;

class TenantCreatorService
{
    /**
     * Flujo completo para IMPORTAR una base de datos existente.
     */
    public static function create(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {

            // 1. Instanciamos sin guardar todavía
            $tenant = new Tenant;

            if (isset($data['id'])) {
                $tenant->id = $data['id'];
            }

            // Llenamos datos básicos
            $tenant->fill(collect($data)->except(['domain', 'password_confirmation'])->toArray());

            // 2. Guardamos silenciosamente evitando la intervención del plugin
            $tenant->saveQuietly();

            // 3. Le agregamos el Super Admin y metadata que usa el plugin
            DB::table('tenants')->where('id', $tenant->id)->update([
                'user_id' => auth()->id(),
                'data' => json_encode([
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                    'tenancy_db_name' => $tenant->id,
                ]),
            ]);

            // 4. Creamos el sub dominio
            $tenant->domains()->create(['domain' => $data['domain']]);

            // 5. Preparamos la base de datos del Tenant (Admin, Migraciones, etc.)
            self::setup($tenant, $data['password'] ?? null, runMigrations: true, runSeeds: true);

            return $tenant;

        });
    }

    /**
     * Configuración del entorno del Tenant
     */
    public static function setup(

        Tenant $tenant,
        ?string $plainPassword = null,
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

                // 1. Obtenemos el repositorio y le forzamos la conexión
                $migrator->getRepository()->setSource('dynamic');

                // 2. Ahora verificamos si existe la db del tenant
                if (! $migrator->getRepository()->repositoryExists()) {
                    $migrator->getRepository()->createRepository();
                }

                // 3. Ejecutar las migraciones
                $migrator->usingConnection('dynamic', function () use ($migrator, $migrationPath) {
                    $migrator->run([$migrationPath]);
                });
            }

            // --- B. SEEDERS ---
            if ($runSeeds && class_exists(DatabaseSeeder::class)) {
                try {
                    $seeder = app(DatabaseSeeder::class);
                    $seeder->__invoke();
                } catch (\Exception $e) {
                    \Log::error("Seeder error en tenant {$tenant->id}: ".$e->getMessage());
                }
            }

            // --- C. CREACIÓN DE USUARIOS ---

            // --- USUARIO SUPER ADMIN ---

            $superAdminMail = env('SUPER_ADMIN_EMAIL', 'admin@holdingtec.app');

            // 1. Definir contraseña según el entorno
            if (App::isProduction()) {

                // Producción: Generar contraseña aleatoria y segura
                $superAdminPass = Str::random(16);

                Mail::to($superAdminMail)->send(
                    new TenantCredentialsMail($tenant->name, $superAdminPass)
                );

            } else {
                // Local: Usar variable de entorno o 'password' por defecto (nunca el email)
                $superAdminPass = env('SUPER_ADMIN_PASSWORD', 'password');
            }

            $superAdmin = User::updateOrCreate(

                ['email' => $superAdminMail],
                [
                    'name' => 'HoldingTec',
                    'password' => bcrypt($superAdminPass),
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

            // --- USUARIO ADMIN ---
            $admin = User::updateOrCreate(
                ['email' => $tenant->email],
                [
                    'name' => $tenant->name,
                    'password' => $tenant->password, // Hash ya viene del form
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $admin->assignRole(
                auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)
                    ? RoleEnum::SUPER_ADMIN->value
                    : RoleEnum::ADMIN->value
            );

        } catch (\Throwable $e) {
            // Capturamos cualquier error fatal del bloque general
            report($e);
            throw $e; // Re-lanzamos para que Filament sepa que falló
        } finally {
            // Limpia el contexto del tenant
            Tenancy::end();
        }
    }
}
