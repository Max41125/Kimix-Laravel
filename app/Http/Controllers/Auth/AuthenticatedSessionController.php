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
        // Валидация полей
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean', // Для поддержки Remember Me
        ]);
    
        // Аутентификация пользователя
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
    
            // Генерация API токена
            $token = $user->generateToken();
    
            // Установка remember_token, если включен remember me
            if ($request->remember) {
                $user->setRememberToken(Str::random(60)); // Установим случайный токен
                $user->save();
            }
    
            return response()->json(['message' => 'Login successful', 'token' => $token], 200);
        }
    
        // Неверные учетные данные
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    

    public function destroy(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
