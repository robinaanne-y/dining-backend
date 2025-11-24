<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiningTableRequest;
use App\Models\Restaurant;
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

    
}
