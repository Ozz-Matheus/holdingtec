<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $superAdminRole = Role::firstOrCreate([
            'name' => RoleEnum::SUPER_ADMIN->value,
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => RoleEnum::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $panelRole = Role::firstOrCreate([
            'name' => RoleEnum::PANEL_USER->value,
            'guard_name' => 'web',
        ]);

        // Crear permisos base
        $permissions = [
            'View:Role',
            'ViewAny:Role',
            'Create:Role',
            'Update:Role',
        ];

        // Crear permisos si no existen
        $permissionModels = collect($permissions)->map(function ($name) {
            return Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        });

        // Asignar permisos al rol SuperAdmin
        $superAdminRole->givePermissionTo($permissionModels);

    }
}
