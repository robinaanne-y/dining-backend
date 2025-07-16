<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    /**
     * Register a new user.
     * This method handles the registration of a new user, validating the input data,
     * creating a new user record in the database, and returning a success response.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone_number' => $request->phone_number,
            'is_guest' => $request->is_guest ? 1 : 0
        ]);

        return response()->json(['user' => $user], 201);
    }
}
