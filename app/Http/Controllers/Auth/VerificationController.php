<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        // Ищем пользователя по ID
        $user = User::findOrFail($id);

        // Проверяем, что хэш совпадает с email пользователя
        if (! Hash::check($user->getEmailForVerification(), $hash)) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        // Проверяем, не верифицирован ли email уже
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        // Помечаем email как верифицированный
        $user->markEmailAsVerified();

        // Генерируем событие, что email был верифицирован
        event(new Verified($user));

        // Переадресация или успешный ответ
        return redirect(env('FRONTEND_URL') . '/verification-success')->with('message', 'Email verified successfully.');
    
    }
}
