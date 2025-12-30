<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\DiningTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{

    use RefreshDatabase, WithFaker;


    /** @test */
    public function a_customer_can_place_an_order()
    {
        $restaurant = Restaurant::factory()->create(['active' => true]);
        $menuItems = MenuItem::factory()->count(3)->create(['restaurant_id' => $restaurant->id]);
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => $qr = 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);

        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum');

        $orderData = [
            'user_id' => $customer->id,
            'items' => $menuItems->map(function ($item) {
                return [
                    'menu_item_id' => $item->id,
                    'quantity' => rand(1, 5),
                ];
            })->toArray(),
            'qr_token' => $qr
        ];

        $response = $this->postJson("/api/customer/restaurants/{$restaurant->id}/orders", $orderData);
        
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'dining_table_id' => $diningTable->id,
        ]);

        $this->assertDatabaseCount('order_items', count($orderData['items']));
    }

    /** @test */
    public function order_creation_fails_with_invalid_qr_token()
    {
        $restaurant = Restaurant::factory()->create(['active' => true]);
        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum');

        $orderData = [
            'user_id' => $customer->id,
            'items' => [
                ['menu_item_id' => 1, 'quantity' => 2],
            ],
            'qr_token' => 'invalid-token'
        ];

        $response = $this->postJson("/api/customer/restaurants/{$restaurant->id}/orders", $orderData);

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    /** @test */
    public function order_creation_fails_with_no_items()
    {
        $restaurant = Restaurant::factory()->create(['active' => true]);
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => $qr = 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);

        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum');

        $orderData = [
            'user_id' => $customer->id,
            'items' => [],
            'qr_token' => $qr
        ];

        $response = $this->postJson("/api/customer/restaurants/{$restaurant->id}/orders", $orderData);

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    /** @test */
    public function order_creation_fails_with_invalid_menu_item()
    {
        $restaurant = Restaurant::factory()->create(['active' => true]);
        $menuItem = MenuItem::factory()->create(['restaurant_id' => $restaurant->id]);
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => $qr = 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);

        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum');

        $orderData = [
            'user_id' => $customer->id,
            'items' => [
                ['menu_item_id' => $menuItem->id, 'quantity' => 2],
                ['menu_item_id' => 9999, 'quantity' => 1], // Invalid menu item ID
            ],
            'qr_token' => $qr
        ];

        $response = $this->postJson("/api/customer/restaurants/{$restaurant->id}/orders", $orderData);

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        $admin = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create([
            'active' => true,
            'user_id' => $admin->id]);
        $this->actingAs($admin, 'sanctum');

        $customer = User::factory()->create();
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);

        $order = \App\Models\Order::factory()->create([
            'user_id' => $customer->id,
            'dining_table_id' => $diningTable->id,
            'status' => 'pending',
            'total_price' => 100,
        ]);

        $response = $this->put("/api/orders/{$order->id}", [
            'status' => 'completed',
            'user_id' => $restaurant->user_id
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function non_admin_cannot_update_order_status()
    {
        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum');

        $restaurant = Restaurant::factory()->create(['active' => true]);
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);

        $order = \App\Models\Order::factory()->create([
            'user_id' => $customer->id,
            'dining_table_id' => $diningTable->id,
            'status' => 'pending',
            'total_price' => 100,
        ]);

        $response = $this->put("/api/orders/{$order->id}", [
            'status' => 'completed',
            'user_id' => $restaurant->user_id
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function admin_can_create_order_for_own_restaurant()
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create([
            'active' => true,
            'user_id' => $owner->id]);
        $menuItems = MenuItem::factory()->count(3)->create(['restaurant_id' => $restaurant->id]);
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => $qr = 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);
        $this->actingAs($owner, 'sanctum');

        $orderData = [
            'user_id' => $owner->id,
            'items' => $menuItems->map(function ($item) {
                return [
                    'menu_item_id' => $item->id,
                    'quantity' => rand(1, 5),
                ];
            })->toArray(),
            'qr_token' => $qr
        ];

        $response = $this->postJson("/api/restaurants/{$restaurant->id}/orders", $orderData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            'user_id' => $owner->id,
            'dining_table_id' => $diningTable->id,
        ]);
    }

    /** @test */
    public function admin_cannot_create_order_for_other_restaurant()
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $otherOwner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create([
            'active' => true,
            'user_id' => $owner->id]);
        $menuItems = MenuItem::factory()->count(3)->create(['restaurant_id' => $restaurant->id]);
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => $qr = 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);
        $this->actingAs($otherOwner, 'sanctum');

        $orderData = [
            'user_id' => $otherOwner->id,
            'items' => $menuItems->map(function ($item) {
                return [
                    'menu_item_id' => $item->id,
                    'quantity' => rand(1, 5),
                ];
            })->toArray(),
            'qr_token' => $qr
        ];

        $response = $this->postJson("/api/restaurants/{$restaurant->id}/orders", $orderData);
        $response->assertStatus(403);
        $this->assertDatabaseCount('orders', 0);
    }

    /** @test */
    public function check_updated_total_price()
    {
        $restaurant = Restaurant::factory()->create(['active' => true]);
        $menuItems = MenuItem::factory()->count(2)->create([
            'restaurant_id' => $restaurant->id,
            'price' => 10,
        ]);
        $diningTable = DiningTable::factory()->create([
            'table_number' => 1,
            'seating_capacity' => 4,
            'qr_token' => $qr = 'sample-token',
            'restaurant_id' => $restaurant->id
        ]);

        $customer = User::factory()->create();
        $this->actingAs($customer, 'sanctum');

        $orderData = [
            'user_id' => $customer->id,
            'items' => [
                ['menu_item_id' => $menuItems[0]->id, 'quantity' => 2],
                ['menu_item_id' => $menuItems[1]->id, 'quantity' => 3],
            ],
            'qr_token' => $qr
        ];

        $response = $this->postJson("/api/customer/restaurants/{$restaurant->id}/orders", $orderData);
        
        $updatedOrderData = [
            'items' => [
                ['menu_item_id' => $menuItems[0]->id, 'quantity' => 4],
                ['menu_item_id' => $menuItems[1]->id, 'quantity' => 1],
            ],
            'user_id' => $customer->id,
            'qr_token' => $qr
        ];

        $order = \App\Models\Order::first();
        $response = $this->putJson("/api/customer/orders/{$order->id}/add-items", $updatedOrderData);

        $expectedTotal = ($menuItems[0]->price * 6) + ($menuItems[1]->price * 4);
        
        $order = \App\Models\Order::first();
        $response->assertStatus(200);
        $this->assertEquals($expectedTotal, $order->total_price);
    }
}
