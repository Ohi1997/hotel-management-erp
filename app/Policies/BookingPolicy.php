<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'cashier']);
    }

    public function view(User $user, Booking $booking): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('admin');
    }

    public function checkIn(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'cashier']);
    }

    public function checkOut(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'cashier']);
    }
}
