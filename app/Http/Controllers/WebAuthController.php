<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class WebAuthController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Inicio sesion con el usuario creado
        Auth::login($user);

        $request->session()->regenerate();

        return response()->json([
            'message' => 'User created successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],

        ], 201);
    }
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        // Intento de inicio de sesion
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'message' => 'Invalid credentials'
            ]);
        }
        $request->session()->regenerate();

        return response()->json([
            'message' => 'correct login',
            'user' => [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'logged out successfully']);
    }

    /**
     * Obtener usuario autenticado.
     * 
     * Útil para que el frontend verifique si hay sesión activa.
     */
    public function user(Request $request)
    {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ]);
    }
}
