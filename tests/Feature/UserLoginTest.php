<?php

namespace Tests\Feature;

use \App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/api/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertArrayHasKey('access_token', $response->json());
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/api/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertArrayNotHasKey('access_token', $response->json());
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid credentials']);
    }
}
