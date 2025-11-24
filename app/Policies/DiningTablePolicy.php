<?php

namespace App\Policies;

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

    public function create(User $user, int $restaurantUserId) : bool
    {
        return ($this->diningTableRepository->isOwner($user) && $restaurantUserId === $user->id);
    }
}
