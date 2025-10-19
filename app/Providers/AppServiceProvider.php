<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\WakeUp;
use App\Observers\AuditableObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Customer::observe(AuditableObserver::class);
        Booking::observe(AuditableObserver::class);
        Payment::observe(AuditableObserver::class);
        Room::observe(AuditableObserver::class);
        WakeUp::observe(AuditableObserver::class);
    }
}
