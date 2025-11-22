<?php

namespace App\Http\Controllers;

Use App\Http\Requests\MenuItemRequest;
Use App\Repositories\MenuItemRepositoryInterface;
use App\Repositories\RestaurantRepositoryInterface;
Use App\Repositories\UserRepositoryInterface;
Use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;

class MenuItemController extends Controller
{
    
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private UserRepositoryInterface $userRepository,
        private RestaurantRepositoryInterface $restaurantRepository)
    {

    }

    /**
     * Store a newly created menu item in storage.
     *
     * This method handles the creation of a new menu item, validating the input data,
     * checking user permissions, creating a new menu item record in the database,
     * and returning a success response.
     *
     * @param MenuItemRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MenuItemRequest $request)
    {
        Gate::authorize('create-menu-item', [
            $this->restaurantRepository->findById($request->restaurant_id),
            $this->userRepository->findById($request->user_id)
        ]);

        $data = Arr::except($request->validated(), ['user_id']);

        $menuItem = $this->menuItemRepository->create($data);

        return response()->json($menuItem, 201);
    }
}
