<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_OUT_OF_SERVICE = 'out_of_service';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_CLEANING = 'cleaning';

    protected $fillable = [
        'number',
        'room_type_id',
        'floor_id',
        'rate',
        'status',
        'clean_status',
        'is_smoking',
        'amenities',
        'notes',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_smoking' => 'boolean',
        'amenities' => 'array',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
