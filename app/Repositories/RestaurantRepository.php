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

    /**
     * Find a restaurant by the user ID
     * @param int $userId
     * @return Restaurant|null
     */
    public function findByUserId(int $userId): ?Restaurant
    {
        return Restaurant::where('user_id', $userId)->first();
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
