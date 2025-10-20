<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hotel;
use App\Models\HotelSetting;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'manager', 'cashier'];

        $permissions = [
            'manage users',
            'manage roles',
            'manage hotels',
            'manage system settings',
            'manage financial settings',
            'view financial reports',
            'manage operational rules',
            'manage access control',
            'manage housekeeping settings',
            'view audit logs',
            'manage backups',
            'manage notifications',
            'toggle features',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $defaultHotel = Hotel::firstOrCreate(
            ['code' => 'HQ'],
            [
                'name' => 'Headquarters Hotel',
                'timezone' => 'UTC',
                'currency' => 'USD',
                'language' => 'en',
                'is_primary' => true,
                'is_active' => true,
                'default_check_in' => '15:00',
                'default_check_out' => '11:00',
                'default_max_guests' => 2,
                'default_deposit' => 0,
            ]
        );

        HotelSetting::updateOrCreate(
            ['hotel_id' => $defaultHotel->id, 'group' => 'booking'],
            ['values' => [
                'check_in_time' => '15:00',
                'check_out_time' => '11:00',
                'max_guests' => 2,
                'default_deposit' => 0,
                'cancellation_policy' => '24-hour cancellation required.',
            ]]
        );

        HotelSetting::updateOrCreate(
            ['hotel_id' => $defaultHotel->id, 'group' => 'invoice'],
            ['values' => [
                'prefix' => 'INV-' . now()->year . '-',
                'tax_percentage' => 12,
                'service_charge' => 10,
                'footer_text' => 'Thank you for staying with us.',
            ]]
        );

        HotelSetting::updateOrCreate(
            ['hotel_id' => $defaultHotel->id, 'group' => 'notifications'],
            ['values' => [
                'booking_confirmation' => 'Dear {{ guest_name }}, your booking {{ booking_reference }} is confirmed.',
                'cancellation' => 'We are sorry to see you go. Your booking has been cancelled.',
                'check_out' => 'We hope you enjoyed your stay. Please review your invoice.',
            ]]
        );

        HotelSetting::updateOrCreate(
            ['hotel_id' => $defaultHotel->id, 'group' => 'financial'],
            ['values' => [
                'vat_rate' => 12,
                'service_charge_rate' => 10,
                'payment_modes' => ['cash', 'card', 'bank transfer'],
                'gateway_provider' => null,
                'gateway_merchant_id' => null,
                'gateway_api_key' => null,
            ]]
        );

        HotelSetting::updateOrCreate(
            ['hotel_id' => $defaultHotel->id, 'group' => 'operational'],
            ['values' => [
                'housekeeping_start' => '08:00',
                'housekeeping_end' => '16:00',
                'maintenance_window' => 'Sunday 10:00-14:00',
                'housekeeping_notes' => 'Knock twice before entering.',
            ]]
        );

        HotelSetting::updateOrCreate(
            ['hotel_id' => $defaultHotel->id, 'group' => 'access'],
            ['values' => [
                'feature_wake_up_calls' => true,
                'feature_loyalty_program' => false,
                'feature_maintenance' => true,
                'role_permissions_ui' => true,
            ]]
        );

        HotelSetting::updateOrCreate(
            ['hotel_id' => $defaultHotel->id, 'group' => 'audit'],
            ['values' => [
                'retain_logs_for_days' => 90,
                'backup_enabled' => true,
                'backup_frequency' => 'daily',
            ]]
        );

        $rolePermissions = [
            'admin' => $permissions,
            'manager' => [
                'manage operational rules',
                'manage housekeeping settings',
                'manage notifications',
                'view financial reports',
                'toggle features',
            ],
            'cashier' => [
                'view financial reports',
            ],
        ];

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'hotel_id' => $defaultHotel->id,
            ]
        );
        $admin->assignRole('admin');
        $admin->syncPermissions($permissions);

        // Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'hotel_id' => $defaultHotel->id,
            ]
        );
        $manager->assignRole('manager');
        $manager->syncPermissions($rolePermissions['manager']);

        // Cashier user
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Cashier User',
                'password' => bcrypt('password'),
                'hotel_id' => $defaultHotel->id,
            ]
        );
        $cashier->assignRole('cashier');
        $cashier->syncPermissions($rolePermissions['cashier']);

        foreach ($rolePermissions as $role => $permissionSet) {
            Role::findByName($role)->syncPermissions($permissionSet);
        }
    }
}
