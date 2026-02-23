<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterOwnerTest extends TestCase
{

    use RefreshDatabase, WithFaker;


    /** @test */
    public function register_owner_returns_a_successful_response(): void
    {
        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'user_type' => 'owner',
            'restaurant_name' => $this->faker->company(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(204);
    }

     /** @test */
    public function register_owner_returns_a_failed_response(): void
    {
        $response = $this->post('/register', [
            'user_type' => 'owner',
            'restaurant_name' => $this->faker->company(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ], ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }
}
