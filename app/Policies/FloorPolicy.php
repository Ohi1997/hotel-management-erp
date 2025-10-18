<?php

namespace App\Policies;

use App\Models\Floor;
use App\Models\User;

class FloorPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Floor $floor): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Floor $floor): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Floor $floor): bool
    {
        return $user->hasRole('admin');
    }
}
