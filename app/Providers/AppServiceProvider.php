<?php

namespace App\Providers;

use App\Policies\MenuItemPolicy;
use App\Policies\RestaurantPolicy;
use App\Policies\DiningTablePolicy;
use App\Repositories\RestaurantRepository;
use App\Repositories\RestaurantRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\MenuItemRepository;
use App\Repositories\MenuItemRepositoryInterface;
use App\Repositories\DiningTableRepository;
use App\Repositories\DiningTableRepositoryInterface;
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
            DiningTableRepositoryInterface::class => DiningTableRepository::class,
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

        Gate::define('show-menu-items', [MenuItemPolicy::class, 'index']);
        Gate::define('create-menu-item', [MenuItemPolicy::class, 'create']);
        Gate::define('update-menu-item', [MenuItemPolicy::class, 'update']);
        Gate::define('delete-menu-item', [MenuItemPolicy::class, 'delete']);

        Gate::define('create-dining-table', [DiningTablePolicy::class, 'create']);
    }
}
