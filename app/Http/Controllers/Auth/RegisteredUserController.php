<?php

// app/Http/Controllers/Auth/RegisteredUserController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest; // Создайте этот запрос для валидации
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request)
    {
        // Создаем пользователя
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
    
        // Генерируем событие регистрации
        event(new Registered($user));
    
        // Отправляем уведомление о верификации
        $user->sendEmailVerificationNotification();
    
        return response()->json(['message' => 'User registered successfully. Please check your email for verification.'], 201);
    }
    
}
