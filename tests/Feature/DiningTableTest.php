<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;

class DiningTableTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function owner_can_create_add_a_dining_table(): void
    {
        $user = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->post("/api/dining-tables", [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
            'qr_token' => Str::uuid()->toString(),
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabase('dining_tables', [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);
    }
}
