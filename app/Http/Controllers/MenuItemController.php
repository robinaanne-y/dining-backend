<?php

namespace App\Http\Controllers;

Use App\Http\Requests\MenuItemRequest;
use App\Models\MenuItem;
use App\Models\Restaurant;
Use App\Repositories\MenuItemRepositoryInterface;
use App\Repositories\RestaurantRepositoryInterface;
Use App\Repositories\UserRepositoryInterface;
Use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    
    public function __construct(
        private MenuItemRepositoryInterface $menuItemRepository,
        private UserRepositoryInterface $userRepository,
        private RestaurantRepositoryInterface $restaurantRepository)
    {

    }

    /**
     * Display a listing of the menu items.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Restaurant $restaurant)
    {
        Gate::authorize('show-menu-items', [
            $this->restaurantRepository->findById($restaurant->id),
            $this->userRepository->findById($request->user_id)
        ]);
        $menuItems = $this->menuItemRepository->getAllFromRestaurant($restaurant->id);
        return response()->json([
            'menu_items' => $menuItems
        ], 200);
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

    /**
     * Update the specified menu item in storage.
     * This method handles the updating of an existing menu item, validating the input data,
     * checking user permissions, updating the menu item record in the database,
     * and returning a success response.
     * @param MenuItemRequest $request
     * @param MenuItem $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MenuItemRequest $request, MenuItem $menuItem)
    {
        Gate::authorize('update-menu-item', [
            $this->restaurantRepository->findById($menuItem->restaurant_id),
            $this->userRepository->findById(auth()->id())
        ]);

        $data = Arr::except($request->validated(), ['user_id']);
        
        $menuItem->update($data);

        return response()->json($menuItem, 200);
    }
    

    /**
     * Display a listing of the menu items for customers.
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerIndex(Restaurant $restaurant)
    {
        $menuItems = $this->menuItemRepository->getActiveFromRestaurant($restaurant->id);
        return response()->json([
            'menu_items' => $menuItems
        ], 200);
    }

    /**
     * Remove the specified menu item from storage.
     *
     * This method handles the deletion of an existing menu item,
     * checking user permissions, deleting the menu item record from the database,
     * and returning a success response.
     *
     * @param MenuItem $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(MenuItem $menuItem)
    {
        Gate::authorize('delete-menu-item', [
            $this->restaurantRepository->findById($menuItem->restaurant_id),
            $this->userRepository->findById(auth()->id())
        ]);

        if($menuItem->orderItems()->exists()){
            return response()->json(['error' => 'Cannot delete menu item associated with existing orders.'], 400);
        }

        $menuItem->delete();

        return response()->json(null, 204);
    }
}
