<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);
}
