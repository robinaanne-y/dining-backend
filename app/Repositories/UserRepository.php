<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{

    /** Find a user by their email address
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }


    /** Check if a user is an owner
     * @param mixed $user
     * @return bool
     */
    public function isOwner($user): bool
    {
        return $user->user_type === 'owner';
    }


    /** Find a user by their ID
     * @param int $id
     * @return User|null
     */
    public function findById(int $id)
    {
        return User::find($id);
    }
}
