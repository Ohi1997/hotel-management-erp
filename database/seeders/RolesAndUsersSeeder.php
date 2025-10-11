<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'manager', 'cashier'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
        $admin->assignRole('admin');

        // Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            ['name' => 'Manager User', 'password' => bcrypt('password')]
        );
        $manager->assignRole('manager');

        // Cashier user
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@example.com'],
            ['name' => 'Cashier User', 'password' => bcrypt('password')]
        );
        $cashier->assignRole('cashier');
    }
}
