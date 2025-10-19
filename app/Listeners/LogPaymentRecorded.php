<?php

namespace App\Listeners;

use App\Events\PaymentRecorded;
use Illuminate\Support\Facades\Log;

class LogPaymentRecorded
{
    public function handle(PaymentRecorded $event): void
    {
        Log::info('Payment recorded', [
            'payment_id' => $event->payment->id,
            'booking_id' => $event->payment->booking_id,
            'customer_id' => $event->payment->customer_id,
            'amount' => $event->payment->amount,
            'status' => $event->payment->status,
        ]);
    }
}
