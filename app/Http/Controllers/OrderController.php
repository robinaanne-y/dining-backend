<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepositoryInterface;
use App\Repositories\DiningTableRepositoryInterface;
use App\Repositories\OrderItemRepositoryInterface;
use App\Repositories\MenuItemRepositoryInterface;
use App\Repositories\RestaurantRepositoryInterface;
use App\Http\Requests\OrderRequest;
use App\Repositories\UserRepositoryInterface;
Use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private DiningTableRepositoryInterface $diningTableRepository,
        private OrderItemRepositoryInterface $orderItemRepository,
        private MenuItemRepositoryInterface $menuItemRepository,
        private RestaurantRepositoryInterface $restaurantRepository,
        private UserRepositoryInterface $userRepository
    )
    {

    }

    /** Create a new order
     * @param OrderRequest $request
     * @param int $restaurantId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request, $restaurantId)
    {
        if($this->userRepository->isOwner($request->user())) {
            Gate::authorize('create-order', [
                $this->restaurantRepository->findById($restaurantId),
                $request->user()
            ]);
        }
        $data = $request->validated();
        $data['restaurant_id'] = $restaurantId;

        $diningTableId = $this->diningTableRepository->getIdByQrToken($data['qr_token']);

        $order = $this->orderRepository->create($data, $diningTableId);
        
        $this->createOrderItems($order, $data['items']);
        
        $this->orderRepository->updateTotalPrice($order);

        return response()->json($order, 201);
    }

    /** Update an existing order 
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
    */
    public function update(Request $request, $orderId)
    {   
        $order = $this->orderRepository->findById($orderId);
        Gate::authorize('update-order', [
            $order,
            $request->user()
        ]);
        
        $this->orderRepository->update($request->except('user_id'), $order);
        return response()->json($order, 200);
    }

    public function addOrderItems(Request $request, $orderId)
    {
        $order = $this->orderRepository->findById($orderId);
        Gate::authorize('add-order-item', [
            $order,
            $request->user()
        ]);

        $this->createOrderItems($order, $request->input('items'));
        $this->orderRepository->updateTotalPrice($order);
        
        return response()->json($order, 200);
    }

    public function createOrderItems($order, $items)
    {
        foreach($items as $item) {
            $itemData = [
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'price' => $this->menuItemRepository->getPriceById($item['menu_item_id'])*$item['quantity'],
            ];
            $this->orderItemRepository->create($itemData);
        }
    }
}