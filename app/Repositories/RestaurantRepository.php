<?php

namespace App\Repositories;

use App\Models\Restaurant;

class RestaurantRepository implements RestaurantRepositoryInterface
{
    public function findById(int $id): ?Restaurant
    {
        return Restaurant::find($id);
    }
    
    public function create(array $data): Restaurant
    {
        return Restaurant::create($data);
    }
}
