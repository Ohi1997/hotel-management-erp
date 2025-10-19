<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WakeUp;

class WakeUpPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'cashier']);
    }

    public function view(User $user, WakeUp $wakeUp): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'cashier']);
    }

    public function update(User $user, WakeUp $wakeUp): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'cashier']);
    }

    public function delete(User $user, WakeUp $wakeUp): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
