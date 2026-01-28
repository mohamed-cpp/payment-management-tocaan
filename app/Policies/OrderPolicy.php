<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): Response
    {
        return (int) $order->user_id === (int) $user->id
            ? Response::allow()
            : Response::denyAsNotFound('Order not found'); // 404
    }

    public function update(User $user, Order $order): Response
    {
        return $this->view($user, $order);
    }

    public function delete(User $user, Order $order): Response
    {
        return $this->view($user, $order);
    }
}
