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

    public function show(User $user, Restaurant $restaurant) : bool
    {
        return $this->userRepository->isOwner($user) && $restaurant->user_id === $user->id;
    }
    
    /**
     * Determine if the user can create a restaurant.
     * @param User $user
     * @return bool
     */
    public function create(User $user) : bool
    {
        return $this->userRepository->isOwner($user);
    }

    public function update(User $user, Restaurant $restaurant) : bool
    {
        return ($this->userRepository->isOwner($user) && $restaurant->user_id === $user->id);
    }
}
