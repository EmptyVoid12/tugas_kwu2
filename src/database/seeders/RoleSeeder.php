<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::updateOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'superadmin'],
            ['name' => 'super_admin', 'guard_name' => 'superadmin'],
        );

        Role::updateOrCreate(
            ['name' => 'user', 'guard_name' => 'superadmin'],
            ['name' => 'user', 'guard_name' => 'superadmin'],
        );
    }
}
