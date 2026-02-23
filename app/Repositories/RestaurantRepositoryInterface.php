<?php

namespace App\Repositories;

use App\Models\Restaurant;

interface RestaurantRepositoryInterface
{
    public function findById(int $id): ?Restaurant;
    public function findByUserId(int $userId): ?Restaurant;
    public function create(array $data): Restaurant;
}
