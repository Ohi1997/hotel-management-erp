<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\WakeUp;
use App\Observers\AuditableObserver;
use App\Policies\BookingPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\FloorPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\RoomPolicy;
use App\Policies\RoomTypePolicy;
use App\Policies\WakeUpPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $auditableModels = [
            Booking::class,
            Customer::class,
            Floor::class,
            Payment::class,
            Room::class,
            RoomType::class,
            WakeUp::class,
        ];

        foreach ($auditableModels as $model) {
            $model::observe(AuditableObserver::class);
        }

        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(RoomType::class, RoomTypePolicy::class);
        Gate::policy(Floor::class, FloorPolicy::class);
        Gate::policy(WakeUp::class, WakeUpPolicy::class);
    }
}
