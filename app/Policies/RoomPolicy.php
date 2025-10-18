<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function view(User $user, Room $room): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasRole('manager');
    }
}
