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
        $response = $this->post('/api/register', [
            'name' => $this->faker->name(),
            'user_type' => 'owner',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
        ]);

        $response->assertStatus(201);
    }

     /** @test */
    public function register_owner_returns_a_failed_response(): void
    {
        $response = $this->post('/api/register', [
            'user_type' => 'owner',
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
        ], ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }
}
