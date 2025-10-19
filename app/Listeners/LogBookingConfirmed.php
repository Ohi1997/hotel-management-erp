<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use Illuminate\Support\Facades\Log;

class LogBookingConfirmed
{
    public function handle(BookingConfirmed $event): void
    {
        Log::info('Booking confirmed', [
            'booking_id' => $event->booking->id,
            'reference' => $event->booking->reference,
            'customer_id' => $event->booking->customer_id,
            'status' => $event->booking->status,
        ]);
    }
}
