<?php

namespace App\Repositories;

use App\Models\DiningTable;

class DiningTableRepository implements DiningTableRepositoryInterface
{

    /** Create a new dining table
     * @param array $data
     * @return DiningTable
     */
    public function create(array $data): DiningTable
    {
        return DiningTable::create($data);
    }

    /** Update an existing dining table
     * @param DiningTable $diningTable
     * @param array $data
     * @return DiningTable
     */
    public function update(DiningTable $diningTable, array $data): DiningTable
    {
        $diningTable->update($data);
        return $diningTable;
    }

    /** Check if a user is the owner of a restaurant
     * @param mixed $user
     * @param int $restaurantId
     * @return bool
     */
    public function isOwner($user, $restaurantId): bool
    {
        return $user->restaurant()->where('id', $restaurantId)->exists();
    }

    /** Check if a dining table has any orders
     * @param DiningTable $diningTable
     * @return bool
     */
    public function hasOrders(DiningTable $diningTable) : bool
    {
        return $diningTable->orders()->exists();
    }

    /** Get the ID of a dining table by its QR token
     * @param string $qrToken
     * @return int
     */
    public function getIdByQrToken(string $qrToken): int
    {
        $diningTable = DiningTable::where('qr_token', $qrToken)->firstOrFail();
        return $diningTable->id;
    }
}