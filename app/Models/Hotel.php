<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'tax_id',
        'timezone',
        'currency',
        'language',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'logo_path',
        'default_check_in',
        'default_check_out',
        'default_max_guests',
        'default_deposit',
        'cancellation_policy',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'default_check_in' => 'datetime:H:i',
        'default_check_out' => 'datetime:H:i',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(HotelSetting::class);
    }
}
