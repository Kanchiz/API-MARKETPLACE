<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Método para Iniciar Sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'contrasena' => ['required'],
        ]);

        if (Auth::attempt(['email' => $credentials['correo'], 'password' => $credentials['contrasena']])) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->name,
                    'correo' => $user->email,
                ]
            ], 200);
        }

        return response()->json([
            'message' => 'Credenciales incorrectas'
        ], 401);
    }

    // Método para Registrar Usuario
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:users,email',
            'contrasena' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->nombre,
            'email' => $request->correo,
            'phone' => $request->telefono ?? null,
            'password' => Hash::make($request->contrasena),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->name,
                'correo' => $user->email,
            ]
        ], 201);
    }
}
