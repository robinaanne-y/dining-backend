<?php

namespace App\Repositories;

use App\Models\DiningTable;

class DiningTableRepository implements DiningTableRepositoryInterface
{

    public function create(array $data): DiningTable
    {
        return DiningTable::create($data);
    }

    public function isOwner($user, $restaurantId): bool
    {
        return $user->restaurant()->where('id', $restaurantId)->exists();
    }
}