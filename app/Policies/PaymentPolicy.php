<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'cashier']);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('cashier');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasRole('cashier');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('cashier');
    }
}
