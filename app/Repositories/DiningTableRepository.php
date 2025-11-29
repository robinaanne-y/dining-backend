<?php

namespace App\Repositories;

use App\Models\DiningTable;

class DiningTableRepository implements DiningTableRepositoryInterface
{

    public function create(array $data): DiningTable
    {
        return DiningTable::create($data);
    }

    public function update(DiningTable $diningTable, array $data): DiningTable
    {
        $diningTable->update($data);
        return $diningTable;
    }

    public function isOwner($user, $restaurantId): bool
    {
        return $user->restaurant()->where('id', $restaurantId)->exists();
    }

    public function hasOrders(DiningTable $diningTable) : bool
    {
        return $diningTable->orders()->exists();
    }
}