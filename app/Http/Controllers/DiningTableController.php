<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Http\Requests\DiningTableRequest;
use App\Repositories\DiningTableRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;

class DiningTableController extends Controller
{
    
    public function __construct(    
        private DiningTableRepositoryInterface $diningTableRepository
    ) {

    }

    /** 
     * Store a newly created dining table in storage.
     * @param DiningTableRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DiningTableRequest $request)
    {
        Gate::authorize('create-dining-table', [
            $request->restaurant_id,
            $request->user_id
        ]);
        $data = Arr::except($request->validated(), ['user_id']);

        $diningTable = $this->diningTableRepository->create($data);

        return response()->json($diningTable, 201);
    }

    /** 
     * Update the specified dining table in storage.
     * @param DiningTableRequest $request
     * @param int $diningTableId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DiningTableRequest $request, DiningTable $diningTable)
    {
        Gate::authorize('update-dining-table', [
            $diningTable->restaurant_id,
            $request->user_id
        ]);
        $data = Arr::except($request->validated(), ['user_id']);

        $updatedDiningTable = $this->diningTableRepository->update($diningTable, $data);

        return response()->json($updatedDiningTable, 200);
    }
    
    /** 
     * Remove the specified dining table from storage.
     * @param int $diningTableId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DiningTable $diningTable)
    {
        Gate::authorize('delete-dining-table', [
            $diningTable,
            request()->user_id,
        ]);

        $diningTable->delete();

        return response()->json(null, 200);
    }
}
