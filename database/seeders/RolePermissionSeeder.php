<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage users',
            'manage roles',
            'manage permissions',
            'view dashboard',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(['manage users', 'view dashboard', 'view reports']);

        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->givePermissionTo(['view dashboard', 'view reports']);
    }
}
