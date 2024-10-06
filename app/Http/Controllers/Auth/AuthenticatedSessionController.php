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
        ]);
    
        // Проверяем, передано ли remember для "Запомнить меня"
        $remember = $request->has('remember') ? $request->boolean('remember') : false;
    
        // Логируем попытку входа
        Log::info('Attempting login for:', $credentials);
    
        // Аутентификация пользователя с флагом "Remember me"
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
    
            // Генерация API токена
            $token = $user->generateToken();
    
            return response()->json(['message' => 'Login successful', 'token' => $token], 200);
        }
    
        // Неверные учетные данные
        Log::warning('Invalid login attempt with credentials:', $credentials);
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    
    
    
    

    public function destroy(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
