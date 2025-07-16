<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterCustomerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function register_customer_returns_a_successful_response(): void
    {
        $response = $this->post('/api/register', [
            'name' => $this->faker->name(),
            'user_type' => 'customer',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'is_guest' => false,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $response->json('user.email'),
            'user_type' => 'customer',
        ]);
    }

    /** @test */
    public function register_customer_returns_a_failed_response(): void
    {
        $response = $this->post('/api/register', [
            'name' => '',
            'user_type' => 'customer',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
        ], ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }

    /** @test */
    public function register_customer_as_guest_returns_a_successful_response(): void
    {
        $response = $this->post('/api/register', [
            'name' => $this->faker->name(),
            'user_type' => 'customer',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'is_guest' => true,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $response->json('user.email'),
            'is_guest' => true,
        ]);
    }
}
