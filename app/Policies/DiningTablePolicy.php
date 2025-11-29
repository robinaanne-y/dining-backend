<?php

namespace App\Policies;

use App\Models\DiningTable;
use App\Models\User;
use App\Repositories\DiningTableRepository;

class DiningTablePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(
        private DiningTableRepository $diningTableRepository
    )
    {
        //
    }

    public function create(User $user, int $restaurantId) : bool
    {
        return ($this->diningTableRepository->isOwner($user, $restaurantId));
    }

    public function update(User $user, int $restaurantId) : bool
    {
        return ($this->diningTableRepository->isOwner($user, $restaurantId));
    }

    public function delete(User $user, DiningTable $diningTable) : bool
    {
        return ($this->diningTableRepository->isOwner($user, $diningTable->restaurant_id) && !$this->diningTableRepository->hasOrders($diningTable));
    }
}
