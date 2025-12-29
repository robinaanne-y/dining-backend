<?php

namespace App\Repositories;

use App\Models\DiningTable;

interface DiningTableRepositoryInterface
{
    public function create(array $data) : DiningTable;
    public function update(DiningTable $diningTable, array $data) : DiningTable;
    public function isOwner($user, $restaurantId) : bool;
    public function hasOrders(DiningTable $diningTable) : bool;
    public function getIdByQrToken(string $qrToken): int;
}