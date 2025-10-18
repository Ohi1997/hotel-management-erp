<?php

use App\Livewire\Backoffice\Bookings\Index as BookingIndex;
use App\Livewire\Backoffice\Customers\Index as CustomerIndex;
use App\Livewire\Backoffice\Payments\Index as PaymentIndex;
use App\Livewire\Backoffice\Rooms\Floor as FloorManager;
use App\Livewire\Backoffice\Rooms\RoomType as RoomTypeManager;
use App\Livewire\Backoffice\Rooms\StatusBoard as RoomStatusBoard;
use App\Livewire\Backoffice\WakeUps\Index as WakeUpIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin|manager|cashier'])
    ->prefix('backoffice')
    ->name('backoffice.')
    ->group(function () {
        Route::redirect('/', '/backoffice/dashboard');
        Route::view('/dashboard', 'backoffice.dashboard')->name('dashboard');

        Route::get('/customers', CustomerIndex::class)->name('customers.index');
        Route::get('/bookings', BookingIndex::class)->middleware('role:admin|manager')->name('bookings.index');
        Route::get('/payments', PaymentIndex::class)->middleware('role:admin|cashier')->name('payments.index');

        Route::prefix('rooms')->group(function () {
            Route::get('/board', RoomStatusBoard::class)->middleware('role:admin|manager')->name('rooms.board');
            Route::get('/types', RoomTypeManager::class)->middleware('role:admin')->name('rooms.types');
            Route::get('/floors', FloorManager::class)->middleware('role:admin')->name('rooms.floors');
        });

        Route::get('/wake-ups', WakeUpIndex::class)->name('wake-ups.index');
    });
