<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $data, int $diningTableId): Order
    {
        return Order::create([
            'user_id' => $data['user_id'],
            'dining_table_id' => $diningTableId,
            'status' => $data['status'] ?? 'pending',
            'total_price' => $data['total_price'] ?? 0,
        ]);
    }

    public function update(array $data, Order $order): Order
    {
        $order->update($data);
        return $order;
    }

    public function findById(int $orderId): Order
    {
        return Order::findOrFail($orderId);
    }
    
    public function updateTotalPrice(Order $order, float $totalPrice): Order
    {
        $order->total_price = $totalPrice;
        $order->save();
        return $order;
    }
}