<?php

use App\Livewire\Backoffice\Admin\Roles\Matrix as RolesMatrix;
use App\Livewire\Backoffice\Admin\Settings\Manager as SettingsManager;
use App\Livewire\Backoffice\Admin\Users\Index as AdminUsersIndex;
use App\Livewire\Backoffice\Bookings\Index as BookingsIndex;
use App\Livewire\Backoffice\Customers\Index as CustomersIndex;
use App\Livewire\Backoffice\Payments\Index as PaymentsIndex;
use App\Livewire\Backoffice\Rooms\Floor as FloorsManager;
use App\Livewire\Backoffice\Rooms\RoomType as RoomTypesManager;
use App\Livewire\Backoffice\Rooms\StatusBoard as RoomsStatusBoard;
use App\Livewire\Backoffice\WakeUps\Index as WakeUpsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin|manager|cashier'])->group(function () {
    Route::view('/dashboard', 'backoffice.dashboard')->name('dashboard');

    Route::get('/customers', CustomersIndex::class)
        ->name('customers.index');

    Route::get('/bookings', BookingsIndex::class)
        ->name('bookings.index');

    Route::get('/payments', PaymentsIndex::class)
        ->name('payments.index');

    Route::get('/wake-ups', WakeUpsIndex::class)
        ->name('wakeups.index');
});

Route::prefix('rooms')
    ->name('rooms.')
    ->middleware(['auth', 'verified', 'role:admin|manager'])
    ->group(function () {
        Route::get('/', RoomsStatusBoard::class)->name('board');
        Route::get('/types', RoomTypesManager::class)->name('types');
        Route::get('/floors', FloorsManager::class)->name('floors');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->group(function () {
        Route::get('/users', AdminUsersIndex::class)->name('users');
        Route::get('/roles', RolesMatrix::class)->name('roles');
        Route::get('/settings', SettingsManager::class)->name('settings');
    });
