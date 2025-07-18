<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'user_type' => 'admin',
            'phone_number' => '1234567890',
            'is_guest' => 0,
        ]);

        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => 'password',
            'user_type' => 'customer',
            'phone_number' => '0987654321',
            'is_guest' => 0,
        ]);

        User::factory()->create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => 'password',
            'user_type' => 'customer',
            'phone_number' => '1122334455',
            'is_guest' => 1,
        ]);
    }
}
