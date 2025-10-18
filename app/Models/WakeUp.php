<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WakeUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'scheduled_for',
        'completed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
