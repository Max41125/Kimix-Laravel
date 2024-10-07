<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        // Ищем пользователя по ID
        $user = User::findOrFail($id);


        // Проверяем, не верифицирован ли email уже
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        // Помечаем email как верифицированный
        $user->markEmailAsVerified();

        // Генерируем событие, что email был верифицирован
        event(new Verified($user));

        // Переадресация или успешный ответ
        return redirect('/')->with('message', 'Email verified successfully.');
    }
}
