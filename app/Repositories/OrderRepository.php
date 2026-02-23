<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    
    /** Create a new order
     * @param array $data
     * @param int $diningTableId
     * @return Order
     */
    public function create(array $data, int $diningTableId): Order
    {
        return Order::create([
            'user_id' => $data['user_id'],
            'dining_table_id' => $diningTableId,
            'status' => $data['status'] ?? 'pending',
            'total_price' => $data['total_price'] ?? 0,
        ]);
    }

    /** Update an existing order
     * @param array $data
     * @param Order $order
     * @return Order
     */
    public function update(array $data, Order $order): Order
    {
        $order->update($data);
        return $order;
    }

    /** Find an order by its ID
     * @param int $orderId
     * @return Order
     */
    public function findById(int $orderId): Order
    {
        return Order::findOrFail($orderId);
    }

    /** Update the total price of an order
     * @param Order $order
     * @return Order
     */
    public function updateTotalPrice(Order $order): Order
    {
        $order->total_price = $order->orderItems->sum(function ($item) {
            return $item->price;
        });
        $order->save();
        return $order;
    }

    /**
     * Get all orders for a specific restaurant
     * @param int $restaurantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersForRestaurant(int $restaurantId)
    {
        return Order::whereHas('diningTable', function ($query) use ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        })->with(['diningTable', 'orderItems'])->get();
    }

    public function getOrdersCountForRestaurant(int $restaurantId)
    {
        return Order::selectRaw('status, count(*) as count')
            ->whereHas('diningTable', function ($query) use ($restaurantId) {
                $query->where('restaurant_id', $restaurantId);
            })
            ->groupBy('status')
            ->get();
    }

    /**
     * Get orders for a specific restaurant within a date range
     * @param int $restaurantId
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByDate(int $restaurantId, string $date)
    {
        return Order::whereHas('diningTable', function ($query) use ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        })->whereDate('created_at', $date)->get();
    }
}