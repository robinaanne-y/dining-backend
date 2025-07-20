<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function isAdmin($user): bool
    {
        return $user->user_type === 'admin';
    }

    public function findById(int $id)
    {
        return User::find($id);
    }
}
