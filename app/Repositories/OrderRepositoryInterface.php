<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $data, int $diningTableId) : Order;
    public function update(array $data, Order $order) : Order;
    public function findById(int $orderId) : Order;
    public function updateTotalPrice(Order $order, float $totalPrice) : Order;
}