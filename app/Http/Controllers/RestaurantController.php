<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantRequest;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\RestaurantRepositoryInterface;
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
}
