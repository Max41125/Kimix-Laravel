<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Добавь эту строку

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login successful'], 200);
        }

        Log::error('Login failed for email: ' . $credentials['email']); // Используй класс Log
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
