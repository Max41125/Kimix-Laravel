<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credentials)) {
            // Получаем пользователя для создания токена (если нужно)
            $user = Auth::user();
            $token = $user->generateToken(); // Токен может пригодиться для API
            \Log::info('Login successful for user: ' . Auth::user()->email);
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ], 200);
        }else{

            \Log::error('Login failed for email: ' . $request->email);
        }
    
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    

    public function destroy(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
