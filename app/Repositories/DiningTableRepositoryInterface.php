<?php

namespace App\Repositories;

use App\Models\DiningTable;

interface DiningTableRepositoryInterface
{
    public function create(array $data) : DiningTable;
}