<?php

namespace App\Repositories;

use App\Models\MenuItem;

class MenuItemRepository implements MenuItemRepositoryInterface
{
    public function create(array $data): MenuItem
    {
        return MenuItem::create($data);
    }

}