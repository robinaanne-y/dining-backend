<?php 

namespace App\Repositories;

use App\Models\MenuItem;

interface MenuItemRepositoryInterface
{
    public function create(array $data): MenuItem;
}