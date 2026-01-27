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
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'user_type' => 'customer',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $email=$this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_guest' => false,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(204);
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'user_type' => 'customer',
        ]);
    }

    /** @test */
    public function register_customer_returns_a_failed_response(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'user_type' => 'customer',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ], ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }

    /** @test */
    public function register_customer_as_guest_returns_a_successful_response(): void
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'user_type' => 'customer',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $email=$this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_guest' => true,
        ], ['Accept' => 'application/json']);

        $response->assertStatus(204);
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'is_guest' => true,
        ]);
    }
}
