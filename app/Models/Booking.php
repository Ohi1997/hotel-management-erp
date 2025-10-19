<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_RESERVED = 'reserved';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'reference',
        'customer_id',
        'room_id',
        'status',
        'check_in_at',
        'check_out_at',
        'actual_check_in_at',
        'actual_check_out_at',
        'guest_count',
        'nightly_rate',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'actual_check_in_at' => 'datetime',
        'actual_check_out_at' => 'datetime',
        'nightly_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'guest_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $booking): void {
            if (! $booking->reference) {
                $booking->reference = 'BK-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
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

    public function wakeUps(): HasMany
    {
        return $this->hasMany(WakeUp::class);
    }
}
