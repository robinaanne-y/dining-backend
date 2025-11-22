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
            'status' => 'available',
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_items', [
            'name' => 'New Menu Item',
            'restaurant_id' => $restaurant->id
        ]);
    }

    /** @test */
    public function non_owner_cannot_add_item_to_menu(): void
    {
        $user = User::factory()->create([
            'user_type' => 'customer'
        ]);

        $restaurant = Restaurant::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $response = $this->post('/api/menu-items', [
            'name' => 'New Menu Item',
            'description' => 'Delicious new item',
            'price' => 9.99,
            'category' => 'Appetizers',
            'status' => 'available',
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('menu_items', [
            'name' => 'New Menu Item',
            'restaurant_id' => $restaurant->id
        ]);
    }

    /** @test */
    public function owner_cannot_add_item_to_menu_of_another_restaurant(): void
    {
        $owner1 = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $owner2 = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $owner2->id
        ]);

        Sanctum::actingAs($owner1, ['*']);
        $response = $this->post('/api/menu-items', [
            'name' => 'New Menu Item',
            'description' => 'Delicious new item',
            'price' => 9.99,
            'category' => 'Appetizers',
            'status' => 'available',
            'user_id' => $owner1->id,
            'restaurant_id' => $restaurant->id
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('menu_items', [
            'name' => 'New Menu Item',
            'restaurant_id' => $restaurant->id
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_add_item_to_menu(): void
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->postJson('/api/menu-items', [
            'name' => 'New Menu Item',
            'description' => 'Delicious new item',
            'price' => 9.99,
            'category' => 'Appetizers',
            'status' => 'available',
            'user_id' => null,
            'restaurant_id' => $restaurant->id
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('menu_items', [
            'name' => 'New Menu Item',
            'restaurant_id' => $restaurant->id
        ]);
    }

    /** @test */
    public function owner_can_update_menu_item(): void
    {
        $owner = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $owner->id
        ]);

        $menuItem = $restaurant->menuItems()->create([
            'name' => 'Old Menu Item',
            'description' => 'Old description',
            'price' => 5.99,
            'category' => 'Appetizers',
            'status' => 'available',
        ]);

        Sanctum::actingAs($owner, ['*']);
        $response = $this->put("/api/menu-items/{$menuItem->id}", [
            'name' => 'Updated Menu Item',
            'description' => 'Updated description',
            'price' => 7.99,
            'category' => 'Main Course',
            'status' => 'unavailable',
            'user_id' => $owner->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'name' => 'Updated Menu Item',
            'price' => 7.99,
            'status' => 'unavailable',
        ]);
    }
    
    /** @test */
    public function non_owner_cannot_update_menu_item(): void
    {
        $owner = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $nonOwner = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $owner->id
        ]);

        $menuItem = $restaurant->menuItems()->create([
            'name' => 'Old Menu Item',
            'description' => 'Old description',
            'price' => 5.99,
            'category' => 'Appetizers',
            'status' => 'available',
        ]);

        Sanctum::actingAs($nonOwner, ['*']);
        $response = $this->put("/api/menu-items/{$menuItem->id}", [
            'name' => 'Updated Menu Item',
            'description' => 'Updated description',
            'price' => 7.99,
            'category' => 'Main Course',
            'status' => 'unavailable',
            'user_id' => $nonOwner->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'name' => 'Old Menu Item',
            'price' => 5.99,
            'status' => 'available',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_update_menu_item(): void
    {
        $owner = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $owner->id
        ]);

        $menuItem = $restaurant->menuItems()->create([
            'name' => 'Old Menu Item',
            'description' => 'Old description',
            'price' => 5.99,
            'category' => 'Appetizers',
            'status' => 'available',
        ]);

        $response = $this->putJson("/api/menu-items/{$menuItem->id}", [
            'name' => 'Updated Menu Item',
            'description' => 'Updated description',
            'price' => 7.99,
            'category' => 'Main Course',
            'status' => 'unavailable',
            'user_id' => null,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'name' => 'Old Menu Item',
            'price' => 5.99,
            'status' => 'available',
        ]);
    }

    /** @test */
    public function owner_cannot_update_menu_item_of_another_restaurant(): void
    {
        $owner1 = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $owner2 = User::factory()->create([
            'user_type' => 'owner'
        ]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $owner2->id
        ]);

        $menuItem = $restaurant->menuItems()->create([
            'name' => 'Old Menu Item',
            'description' => 'Old description',
            'price' => 5.99,
            'category' => 'Appetizers',
            'status' => 'available',
        ]);

        Sanctum::actingAs($owner1, ['*']);
        $response = $this->put("/api/menu-items/{$menuItem->id}", [
            'name' => 'Updated Menu Item',
            'description' => 'Updated description',
            'price' => 7.99,
            'category' => 'Main Course',
            'status' => 'available',
            'user_id' => $owner1->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'name' => 'Old Menu Item',
            'price' => 5.99,
            'status' => 'available',
        ]);
    }
    
}
