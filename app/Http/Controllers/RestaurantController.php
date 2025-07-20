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

    public function store(RestaurantRequest $request)
    {

        Gate::authorize('create-restaurant', $this->userRepository->findById($request->user_id));

        $restaurant = $this->restaurantRepository->create($request->validated());

        return response()->json($restaurant, 201);
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize('update-restaurant', [$restaurant, $this->userRepository->findById($request->user_id)]);

        $restaurant->update($request->validated());

        return response()->json($restaurant, 200);
    }
}
