<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MenuTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function restaurant_owner_can_add_item_to_menu(): void
    {
        $user = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $user->id
        ]);

        Sanctum::actingAs($user, ['*']);
        $response = $this->post('/api/menu-items', [
            'name' => 'New Menu Item',
            'description' => 'Delicious new item',
            'price' => 9.99,
            'category' => 'Appetizers',
            'availability' => true,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_items', [
            'name' => 'New Menu Item',
            'restaurant_id' => $restaurant->id
        ]);
    }
}
