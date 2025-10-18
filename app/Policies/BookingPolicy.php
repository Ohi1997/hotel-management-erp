<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('admin') ? true : null;
    }

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
        return $user->hasRole('manager');
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('manager');
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('manager');
    }
}
