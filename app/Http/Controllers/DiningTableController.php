<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiningTableRequest;
use App\Models\Restaurant;
use App\Repositories\DiningTableRepositoryInterface;

class DiningTableController extends Controller
{
    
    public function __construct(    
        private DiningTableRepositoryInterface $diningTableRepository
    ) {

    }

    public function store(DiningTableRequest $request, Restaurant $restaurant)
    {
        $data = $request->validated();
        $data['restaurant_id'] = $restaurant->id;

        $diningTable = $this->diningTableRepository->create($data);

        return response()->json($diningTable, 201);
    }
}
