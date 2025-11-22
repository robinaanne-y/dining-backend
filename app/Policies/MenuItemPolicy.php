<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Restaurant;
use App\Repositories\MenuItemRepository;
use App\Repositories\UserRepositoryInterface;

class MenuItemPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(
        private MenuItemRepository $menuItemRepository,
        private UserRepositoryInterface $userRepository
    ){
    }

    /**
     * Determine if the user can create a menu item.
     * @param User $user
     * @return bool
     */
    public function createMenuItem(User $user, Restaurant $restaurant) : bool
    {
        return ($this->userRepository->isOwner($user) && $restaurant->user_id === $user->id);
    }
}
