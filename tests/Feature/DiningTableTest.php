<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\DiningTable;
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
        $this->assertDatabaseHas('dining_tables', [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);
    }

    /** @test */
    public function non_owner_cannot_add_a_dining_table(): void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $nonOwner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($nonOwner, ['*']);

        $response = $this->post("/api/dining-tables", [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
            'qr_token' => Str::uuid()->toString(),
            'user_id' => $nonOwner->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('dining_tables', [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]); 
    }

    /** @test */
    public function unauthorized_user_cannot_add_a_dining_table(): void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);

        $response = $this->postJson("/api/dining-tables", [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
            'qr_token' => Str::uuid()->toString(),
            'user_id' => $owner->id,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('dining_tables', [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]); 
    }

    /** @test */
    public function customer_cannot_add_a_dining_table(): void
    {
        $customer = User::factory()->create(['user_type' => 'customer']);
        $restaurant = Restaurant::factory()->create();

        Sanctum::actingAs($customer, ['*']);

        $response = $this->post("/api/dining-tables", [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
            'qr_token' => Str::uuid()->toString(),
            'user_id' => $customer->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('dining_tables', [
            'table_number' => '1',
            'seating_capacity' => 4,
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]); 
    }

    /** @test */
    public function owner_can_update_dining_tables(): void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($owner, ['*']);

        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'qr_token' => $qr = Str::uuid()->toString(),
            'status' => 'available',
        ]);

        $updateResponse = $this->put("/api/dining-tables/{$diningTable->id}", [
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
            'user_id' => $owner->id,
            'qr_token' => $qr,
            'restaurant_id' => $restaurant->id,
        ]);
        
        $updateResponse->assertStatus(200);
        $this->assertDatabaseHas('dining_tables', [
            'id' => $diningTable->id,
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
        ]);
    }

    /** @test */
    public function non_owner_cannot_update_dining_tables(): void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $nonOwner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);
        Sanctum::actingAs($nonOwner, ['*']);
        $response = $this->put("/api/dining-tables/{$diningTable->id}", [
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
            'user_id' => $nonOwner->id,
            'qr_token' => $diningTable->qr_token,
            'restaurant_id' => $restaurant->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('dining_tables', [
            'id' => $diningTable->id,
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
        ]);
    }

    /** @test */
    public function unauthorized_cannot_update_dining_tables(): void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);

        $response = $this->putJson("/api/dining-tables/{$diningTable->id}", [
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
            'user_id' => $owner->id,
            'qr_token' => $diningTable->qr_token,
            'restaurant_id' => $restaurant->id,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('dining_tables', [
            'id' => $diningTable->id,
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
        ]);
    }

    /** @test */
    public function customer_cannot_update_dining_tables(): void
    {
        $customer = User::factory()->create(['user_type' => 'customer']);
        $restaurant = Restaurant::factory()->create();
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);

        Sanctum::actingAs($customer, ['*']);
        $response = $this->put("/api/dining-tables/{$diningTable->id}", [
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
            'user_id' => $customer->id,
            'qr_token' => $diningTable->qr_token,
            'restaurant_id' => $restaurant->id,
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('dining_tables', [
            'id' => $diningTable->id,
            'table_number' => '2',
            'seating_capacity' => 6,
            'status' => 'occupied',
        ]);
    }
    
    /** @test */
    public function owner_can_delete_a_dining_table() : void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);

        Sanctum::actingAs($owner, ['*']);

        $response = $this->delete("/api/dining-tables/{$diningTable->id}", [
            'user_id' => $owner->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('dining_tables', [
            'id' => $diningTable->id,
        ]);
    }

    /** @test */
    public function non_owner_cannot_delete_a_dining_table() : void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $nonOwner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);

        Sanctum::actingAs($nonOwner, ['*']);

        $response = $this->delete("/api/dining-tables/{$diningTable->id}", [
            'user_id' => $nonOwner->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('dining_tables', [
            'id' => $diningTable->id,
        ]);
    }

    public function unauthorized_user_cannot_delete_a_dining_table() : void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);

        $response = $this->deleteJson("/api/dining-tables/{$diningTable->id}", [
            'user_id' => $owner->id,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseHas('dining_tables', [
            'id' => $diningTable->id,
        ]);
    }

    /** @test */
    public function customer_cannot_delete_a_dining_table() : void
    {
        $customer = User::factory()->create(['user_type' => 'customer']);
        $restaurant = Restaurant::factory()->create();
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);

        Sanctum::actingAs($customer, ['*']);

        $response = $this->delete("/api/dining-tables/{$diningTable->id}", [
            'user_id' => $customer->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('dining_tables', [
            'id' => $diningTable->id,
        ]);
    }

    /** @test */
    public function owner_cannot_delete_dining_table_with_orders() : void
    {
        $owner = User::factory()->create(['user_type' => 'owner']);
        $restaurant = Restaurant::factory()->create(['user_id' => $owner->id]);
        $diningTable = DiningTable::factory()->create([
            'restaurant_id' => $restaurant->id,
            'status' => 'available',
        ]);

        $order = Order::factory()->create([
            'dining_table_id' => $diningTable->id,
        ]);

        Sanctum::actingAs($owner, ['*']);
        $response = $this->delete("/api/dining-tables/{$diningTable->id}", [
            'user_id' => $owner->id,
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('dining_tables', [
            'id' => $diningTable->id,
        ]);
    }
}
