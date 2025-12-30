<?php

namespace App\Repositories;

use App\Models\MenuItem;

class MenuItemRepository implements MenuItemRepositoryInterface
{

    /** Create a new menu item
     * @param array $data
     * @return MenuItem
     */
    public function create(array $data): MenuItem
    {
        return MenuItem::create($data);
    }

    /** Get all menu items from a restaurant
     * @param int $restaurantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFromRestaurant($restaurantId)
    {
        return MenuItem::where('restaurant_id', $restaurantId)->get();
    }

    /** Get active menu items from a restaurant
     * @param int $restaurantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveFromRestaurant($restaurantId)
    {
        return MenuItem::where('restaurant_id', $restaurantId)
                        ->whereNotIn('status', ['inactive'])
                        ->get();
    }

    /** Get the price of a menu item by its ID
     * @param int $menuItemId
     * @return float
     */
    public function getPriceById(int $menuItemId): float
    {
        $menuItem = MenuItem::findOrFail($menuItemId);
        return $menuItem->price;
    }
}