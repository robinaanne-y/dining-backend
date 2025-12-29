<?php

namespace App\Repositories;

use App\Models\OrderItem;

interface OrderItemRepositoryInterface
{
    public function create(array $data) : OrderItem;
}