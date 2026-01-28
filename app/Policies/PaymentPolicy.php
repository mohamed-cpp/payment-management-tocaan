<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function view(User $user, Payment $payment): bool
    {
        return (int) $payment->order->user_id === (int) $user->id;
    }
}
