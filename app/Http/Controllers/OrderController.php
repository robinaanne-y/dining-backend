<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepositoryInterface;
use App\Repositories\DiningTableRepositoryInterface;
use App\Repositories\OrderItemRepositoryInterface;
use App\Repositories\MenuItemRepositoryInterface;

use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private DiningTableRepositoryInterface $diningTableRepository,
        private OrderItemRepositoryInterface $orderItemRepository,
        private MenuItemRepositoryInterface $menuItemRepository,
    )
    {

    }

    public function store(OrderRequest $request, $restaurantId)
    {
        $data = $request->validated();
        $data['restaurant_id'] = $restaurantId;

        $diningTableId = $this->diningTableRepository->getIdByQrToken($data['qr_token']);

        $order = $this->orderRepository->create($data, $diningTableId);
        
        $items = collect($data['items'])->map(function ($item) use ($order) {
            $itemData = [
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'price' => $this->menuItemRepository->getPriceById($item['menu_item_id']),
            ];
            return $itemData;
        });
        
        $items->each(function ($itemData) {
            $this->orderItemRepository->create($itemData);
        });
        
        $this->orderRepository->updateTotalPrice(
            $order,
            $items->reduce(function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0)
        );

        return response()->json($order, 201);
    }
}
