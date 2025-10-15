<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:20|confirmed'
        ]);
        
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 201);
        
    }

    public function login(Request $request)
     {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:20',
        ]);
        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)){
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'correct login',
            'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 200);

    }

    public function logout(Request $request) 
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'logged out successfully'], 200);
    }
}
