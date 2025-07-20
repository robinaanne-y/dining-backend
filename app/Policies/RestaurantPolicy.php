<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use App\Repositories\RestaurantRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Auth\Access\Response;

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

    public function update(User $user, Restaurant $restaurant) : bool
    {
        return ($this->userRepository->isAdmin($user) && $restaurant->user_id === $user->id);
    }
}
