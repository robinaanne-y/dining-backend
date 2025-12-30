<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use App\Models\Restaurant;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function create(User $user, Restaurant $restaurant) : bool
    {
        return $user->id === $restaurant->user_id;
    }

    public function update(User $user, Order $order) : bool
    {
        return $order->diningTable->restaurant->user_id === $user->id;
    }

    public function addOrderItem(User $user, Order $order) : bool
    {
        return $order->user_id === $user->id;
    }
}
