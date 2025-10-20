<?php

use App\Models\Hotel;
use App\Models\HotelSetting;
use App\Models\User;
use Database\Seeders\RolesAndUsersSeeder;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\seed;

it('seeds default hotels, users, and settings', function (): void {
    seed(RolesAndUsersSeeder::class);

    $hotel = Hotel::where('code', 'HQ')->first();

    expect($hotel)->not()->toBeNull();
    expect($hotel->is_primary)->toBeTrue();
    expect($hotel->settings)->toHaveCount(7);

    $admin = User::whereEmail('admin@example.com')->first();

    expect($admin)->not()->toBeNull();
    expect($admin->hotel_id)->toBe($hotel->id);
    expect($admin->hasRole('admin'))->toBeTrue();
    expect($admin->hasPermissionTo('manage users'))->toBeTrue();

    $manager = User::whereEmail('manager@example.com')->first();
    expect($manager)->not()->toBeNull();
    expect($manager->hasRole('manager'))->toBeTrue();
    expect($manager->hasPermissionTo('toggle features'))->toBeTrue();

    $cashier = User::whereEmail('cashier@example.com')->first();
    expect($cashier)->not()->toBeNull();
    expect($cashier->hasRole('cashier'))->toBeTrue();
    expect($cashier->hasPermissionTo('view financial reports'))->toBeTrue();

    $bookingSettings = HotelSetting::where('hotel_id', $hotel->id)
        ->where('group', 'booking')
        ->firstOrFail()
        ->values;

    expect($bookingSettings['check_in_time'])->toBe('15:00');
    expect($bookingSettings['check_out_time'])->toBe('11:00');
    expect($bookingSettings['max_guests'])->toBe(2);

    $financialSettings = HotelSetting::where('hotel_id', $hotel->id)
        ->where('group', 'financial')
        ->firstOrFail()
        ->values;

    expect($financialSettings['payment_modes'])->toContain('cash', 'card');
    expect($financialSettings['vat_rate'])->toEqual(12);
    expect($financialSettings['service_charge_rate'])->toEqual(10);

    expect(Permission::where('name', 'manage hotels')->exists())->toBeTrue();
});
