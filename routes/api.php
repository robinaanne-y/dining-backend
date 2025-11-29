<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\DiningTableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegistrationController::class, 'register']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show']);
    Route::post('/restaurants', [RestaurantController::class, 'store']);
    Route::put('/restaurants/{restaurant}', [RestaurantController::class, 'update']);
    Route::put('/restaurants/{restaurant}/deactivate', [RestaurantController::class, 'deactivate']);
    Route::put('/restaurants/{restaurant}/activate', [RestaurantController::class, 'activate']);

    Route::post('/restaurants/{restaurant}/menu-items', [MenuItemController::class, 'index']);
    Route::post('/menu-items', [MenuItemController::class, 'store']);
    Route::put('/menu-items/{menuItem}', [MenuItemController::class, 'update']);
    Route::delete('/menu-items/{menuItem}', [MenuItemController::class, 'destroy']);
    
    Route::post('/dining-tables', [DiningTableController::class, 'store']);
    Route::put('/dining-tables/{diningTable}', [DiningTableController::class, 'update']);
});

Route::prefix('/customer')->group(function () {
    // Route::get('/restaurants', [RestaurantController::class, 'customerIndex']);
    Route::get('/restaurants/{restaurant}/menu-items', [MenuItemController::class, 'customerIndex']);
});
