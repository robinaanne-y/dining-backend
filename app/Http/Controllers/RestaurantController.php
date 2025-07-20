<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantRequest;
use App\Models\Restaurant;
use App\Repositories\RestaurantRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{

    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
        private UserRepositoryInterface $userRepository)
    {

    }


    /**
     * Display the specified restaurant.
     * This method retrieves a restaurant by its ID and checks if the user has permission to view it.
     * If the user is authorized, it returns the restaurant data in JSON format.
     * @param Restaurant $restaurant
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Restaurant $restaurant)
    {
        Gate::authorize('view-restaurant', [$restaurant, $this->userRepository->findById(auth()->id())]);
        return response()->json($restaurant, 200);
    }

    /**
     * Store a new restaurant.
        * This method handles the creation of a new restaurant, validating the input data,
        * creating a new restaurant record in the database, and returning a success response.
        * @param Request $request
        * @return \Illuminate\Http\JsonResponse
     */
    public function store(RestaurantRequest $request)
    {

        Gate::authorize('create-restaurant', $this->userRepository->findById($request->user_id));

        $restaurant = $this->restaurantRepository->create($request->validated());

        return response()->json($restaurant, 201);
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize('update-restaurant', [$restaurant, $this->userRepository->findById(auth()->id())]);

        $restaurant->update($request->validated());

        return response()->json($restaurant, 200);
    }

    public function activate(Restaurant $restaurant)
    {
        Gate::authorize('update-restaurant', [$restaurant, $this->userRepository->findById(auth()->id())]);

        $restaurant->update(['active' => true]);

        return response()->json($restaurant, 200);
    }
    
    public function deactivate(Restaurant $restaurant)
    {
        Gate::authorize('update-restaurant', [$restaurant, $this->userRepository->findById(auth()->id())]);

        $restaurant->update(['active' => false]);

        return response()->json($restaurant, 200);
    }
}
