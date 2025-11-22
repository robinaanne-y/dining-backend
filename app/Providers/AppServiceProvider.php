<?php

namespace App\Providers;

use App\Policies\MenuItemPolicy;
use App\Policies\RestaurantPolicy;
use App\Repositories\RestaurantRepository;
use App\Repositories\RestaurantRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\MenuItemRepository;
use App\Repositories\MenuItemRepositoryInterface;
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
            MenuItemRepositoryInterface::class => MenuItemRepository::class,
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
        Gate::define('view-restaurant', [RestaurantPolicy::class, 'show']);
        
        Gate::define('create-menu-item', [MenuItemPolicy::class, 'create']);
        Gate::define('update-menu-item', [MenuItemPolicy::class, 'update']);
    }
}
