<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);
    public function isOwner($user): bool;
    public function findById(int $id);
}
