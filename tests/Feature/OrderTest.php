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
        //dd($response->json());
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'dining_table_id' => $diningTable->id,
        ]);

        $this->assertDatabaseCount('order_items', count($orderData['items']));
    }
}
