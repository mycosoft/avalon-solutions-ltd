<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@avalon.com',
            'password' => Hash::make('password123'),
            'phone' => '+256700000000',
            'role_type' => 'superadmin',
            'status' => true,
        ]);
        $superadmin->assignRole('superadmin');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@avalon.com',
            'password' => Hash::make('password123'),
            'phone' => '+256700000001',
            'role_type' => 'admin',
            'status' => true,
        ]);
        $admin->assignRole('admin');

        $accountant = User::create([
            'name' => 'Accountant User',
            'email' => 'accountant@avalon.com',
            'password' => Hash::make('password123'),
            'phone' => '+256700000002',
            'role_type' => 'accountant',
            'status' => true,
        ]);
        $accountant->assignRole('accountant');
    }
}
