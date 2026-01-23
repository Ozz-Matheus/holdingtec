<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate([
            'name' => RoleEnum::SUPER_ADMIN->value,
            'guard_name' => 'web',
        ]);

        $email = env('SUPER_ADMIN_EMAIL', 's@holdingtec.app');
        $password = env('SUPER_ADMIN_PASSWORD', $email);

        $superAdmin = new User;
        $superAdmin->name = 'HoldingTec Admin';
        $superAdmin->email = $email;
        $superAdmin->password = bcrypt($password);
        $superAdmin->save();

        $superAdmin->assignRole($superAdminRole);
    }
}
