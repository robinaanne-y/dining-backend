<?php

namespace App\Policies;

use App\Models\User;
use App\Repositories\RestaurantRepositoryInterface;
use App\Repositories\UserRepositoryInterface;

class RestaurantPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository, 
        private UserRepositoryInterface $userRepository
        )
    {
        //
    }

    public function create(User $user) : bool
    {
        return $this->userRepository->isAdmin($user);
    }

    public function update(User $user) : bool
    {
        return $this->userRepository->isAdmin($user);
    }
}
