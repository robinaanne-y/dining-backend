<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {

    }

    /**
     * Get the authenticated user's information
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser()
    {
        $user = auth()->user();
        return response()->json($user, 200);
    }
}
