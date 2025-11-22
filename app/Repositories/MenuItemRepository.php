<?php

namespace App\Repositories;

use App\Models\MenuItem;

class MenuItemRepository implements MenuItemRepositoryInterface
{

    public function create(array $data): MenuItem
    {
        return MenuItem::create($data);
    }

    public function getAllFromRestaurant($restaurantId)
    {
        return MenuItem::where('restaurant_id', $restaurantId)->get();
    }

    public function getActiveFromRestaurant($restaurantId)
    {
        return MenuItem::where('restaurant_id', $restaurantId)
                        ->whereNotIn('status', ['inactive'])
                        ->get();
    }
}