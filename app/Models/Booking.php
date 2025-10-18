<?php

namespace App\Models;

use App\Events\BookingConfirmed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'customer_id',
        'room_id',
        'check_in_at',
        'check_out_at',
        'status',
        'total_amount',
        'balance_due',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::updated(function (self $booking) {
            if ($booking->isDirty('status') && $booking->status === 'confirmed') {
                BookingConfirmed::dispatch($booking);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
