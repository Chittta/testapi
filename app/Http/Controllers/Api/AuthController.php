<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        return "Login";
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        $token = $user->createToken('aaafff')->plainTextToken;

        return response()->json([
            'message'      => 'User registered successfully',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ], 201);
    }

    
}
