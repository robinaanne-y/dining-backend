<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function create(array $data, int $diningTableId) : Order;
    public function update(array $data, Order $order) : Order;
    public function findById(int $orderId) : Order;
    public function updateTotalPrice(Order $order) : Order;
    public function getOrdersForRestaurant(int $restaurantId);
    public function getOrdersCountForRestaurant(int $restaurantId);
    public function getOrdersByDate(int $restaurantId, string $date);
}