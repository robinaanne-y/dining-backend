<?php

namespace Tests\Feature;

use \App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
