<?php

namespace App\Providers;

use App\Policies\RestaurantPolicy;
use App\Repositories\RestaurantRepository;
use App\Repositories\RestaurantRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $bindings = [
            // Bind interfaces to implementations
            UserRepositoryInterface::class => UserRepository::class,
            RestaurantRepositoryInterface::class => RestaurantRepository::class,
        ];

        foreach ($bindings as $interface => $implementation) {
            if (class_exists($implementation)) {
                $this->app->bind($interface, $implementation);
            } else {
                throw new \RuntimeException("Implementation class {$implementation} does not exist for interface {$interface}");
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('create-restaurant', [RestaurantPolicy::class, 'create']);
        Gate::define('update-restaurant', [RestaurantPolicy::class, 'update']);
    }
}
