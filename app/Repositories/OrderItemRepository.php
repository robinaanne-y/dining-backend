<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository implements OrderItemRepositoryInterface
{

    /** Create a new order item
     * @param array $data
     * @return OrderItem
     */
    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }
}