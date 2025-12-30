<?php

namespace App\Repositories;

use App\Models\Restaurant;

class RestaurantRepository implements RestaurantRepositoryInterface
{

    /** Find a restaurant by its ID
     * @param int $id
     * @return Restaurant|null
     */
    public function findById(int $id): ?Restaurant
    {
        return Restaurant::findOrFail($id);
    }

    /** Create a new restaurant
     * @param array $data
     * @return Restaurant
     */
    public function create(array $data): Restaurant
    {
        return Restaurant::create($data);
    }
}
