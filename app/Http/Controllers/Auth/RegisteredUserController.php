<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest; // Создайте этот запрос для валидации
use App\Models\User;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Теперь будет автоматически хэшироваться
            'role' => $request->role,
        ]);
    
        return response()->json(['message' => 'User registered successfully'], 201);
    }
    
}
