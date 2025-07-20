<?php

namespace Tests\Feature;

use \App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function admin_can_register_a_restaurant(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $this->actingAs($user);
        $response = $this->post('/api/restaurants', Restaurant::factory()->make([
            'user_id' => $user->id
        ])->toArray());

        
        $response->assertStatus(201);
        $this->assertDatabaseHas('restaurants', [
            'user_id' => 1,
        ]);
    }

    /** @test */
    public function unauthenticated_admin_cannot_register_a_restaurant(): void
    {   
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $response = $this->post('/api/restaurants', Restaurant::factory()->make([
            'user_id' => $user->id
        ])->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized access']);
        $this->assertDatabaseMissing('restaurants', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function customer_cannot_register_a_restaurant(): void
    {
        $user = User::factory()->create([
            'user_type' => 'customer'
        ]);
        $this->actingAs($user);
        $response = $this->post('/api/restaurants', Restaurant::factory()->make([
            'user_id' => $user->id
        ])->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
        $this->assertDatabaseMissing('restaurants', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function admin_can_update_a_restaurant(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $this->actingAs($user);
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id, Restaurant::factory()->make([
            'user_id' => $user->id,
            'name' => 'Updated Restaurant Name'
        ])->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => 'Updated Restaurant Name',
        ]);
    }

    /** @test */
    public function unauthenticated_admin_cannot_update_a_restaurant(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id, Restaurant::factory()->make([
            'user_id' => $user->id,
            'name' => 'Updated Restaurant Name'
        ])->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized access']);
        $this->assertDatabaseMissing('restaurants', [
            'id' => $restaurant->id,
            'name' => 'Updated Restaurant Name',
        ]);
    }

    /** @test */
    public function customer_cannot_update_a_restaurant(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $customer = User::factory()->create([
            'user_type' => 'customer'
        ]);
        $this->actingAs($customer);
        $restaurant = Restaurant::factory()->create(['user_id' => $admin->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id, Restaurant::factory()->make([
            'user_id' => $admin->id,
            'name' => 'Updated Restaurant Name'
        ])->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
        $this->assertDatabaseMissing('restaurants', [
            'id' => $restaurant->id,
            'name' => 'Updated Restaurant Name',
        ]);
    }

    /** @test */
    public function admin_cannot_update_other_users_restaurant(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $otherUser = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $this->actingAs($admin);
        $restaurant = Restaurant::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id, Restaurant::factory()->make([
            'user_id' => $otherUser->id,
            'name' => 'Updated Restaurant Name'
        ])->toArray(), ['Accept' => 'application/json']);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
        $this->assertDatabaseMissing('restaurants', [
            'id' => $restaurant->id,
            'name' => 'Updated Restaurant Name',
        ]);
    }

    /** @test */
    public function admin_can_get_restaurant_details(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $this->actingAs($user);
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/api/restaurants/' . $restaurant->id, ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'user_id' => $restaurant->user_id,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_restaurant_details(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/api/restaurants/' . $restaurant->id, ['Accept' => 'application/json']);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized access']);
    }

    /** @test */
    public function customer_cannot_get_restaurant_details(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $customer = User::factory()->create([
            'user_type' => 'customer'
        ]);
        $this->actingAs($customer);
        $restaurant = Restaurant::factory()->create(['user_id' => $admin->id]);

        $response = $this->get('/api/restaurants/' . $restaurant->id, ['Accept' => 'application/json']);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /** @test */
    public function admin_cannot_get_other_users_restaurant_details(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $otherUser = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $this->actingAs($admin);
        $restaurant = Restaurant::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->get('/api/restaurants/' . $restaurant->id, ['Accept' => 'application/json']);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /** @test */
    public function admin_can_deactivate_a_restaurant(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $this->actingAs($user);
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id .'/deactivate', ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'active' => false,
        ]);
    }

    /** @test */
    public function unauthenticated_admin_cannot_deactivate_a_restaurant(): void
    {
        $user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id .'/deactivate', [], ['Accept' => 'application/json']);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized access']);
        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'active' => true, // restaurant should still be active
        ]);
    }

    /** @test */
    public function customer_cannot_deactivate_a_restaurant(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $customer = User::factory()->create([
            'user_type' => 'customer'
        ]);
        $this->actingAs($customer);
        $restaurant = Restaurant::factory()->create(['user_id' => $admin->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id .'/deactivate', ['Accept' => 'application/json']);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'active' => true,
        ]);
    }

    /** @test */
    public function other_admin_cannot_deactivate_another_users_restaurant(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $otherUser = User::factory()->create([
            'user_type' => 'admin'
        ]);
        $this->actingAs($admin);
        $restaurant = Restaurant::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->put('/api/restaurants/' . $restaurant->id .'/deactivate', ['Accept' => 'application/json']);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'active' => true,
        ]);
    }
}
