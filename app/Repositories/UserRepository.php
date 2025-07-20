<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function isOwner($user): bool
    {
        return $user->user_type === 'owner';
    }

    public function findById(int $id)
    {
        return User::find($id);
    }
}
