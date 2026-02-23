<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    )
    {

    }

    public function getOrders()
    {
        $user = auth()->user();
        $orders = $this->orderRepository->getOrdersForRestaurant($user->restaurant->id);

        return response()->json($orders, 200);
    }

    public function getOrdersCount()
    {
        $user = auth()->user();
        $ordersCount = $this->orderRepository->getOrdersCountForRestaurant($user->restaurant->id);

        return response()->json($ordersCount, 200);
    }

    public function getSalesData()
    {
        $user = auth()->user();
        $sales = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $total = $this->orderRepository->getOrdersByDate($user->restaurant->id, $date)
                ->sum('total_price');
                
            $sales->push([
                'day' => $date->format('D'),
                'sales' => $total,
            ]);
        }

        return response()->json($sales);
    }
}
