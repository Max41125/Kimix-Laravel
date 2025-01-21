<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PasswordResetController extends Controller
{
    /**
     * Отправка ссылки для сброса пароля.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $response = Password::sendResetLink(
            $request->only('email')
        );

        return $response === Password::RESET_LINK_SENT
            ? response()->json(['message' => Lang::get($response)], 200)
            : response()->json(['message' => Lang::get($response)], 400);
    }
    public function showResetForm($token)
    {
        $email = request('email');  // Извлекаем email из запроса
    
        // Проверяем, есть ли email в запросе
        if (!$email) {
            return response()->json(['message' => 'Email не передан'], 400);
        }
    
        // Перенаправляем на фронтенд с токеном и email в URL
        return redirect()->to(env('FRONTEND_URL') . './auth/reset-password?token=' . $token . '&email=' . urlencode($email));
    }
    
    
    
    /**
     * Сброс пароля через токен.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Используем мутатор для установки нового пароля
                $user->password = $password; 
                $user->save();
            }
        );

        return $response === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Пароль успешно сброшен'], 200)
            : response()->json(['error' => 'Не удалось сбросить пароль'], 400);
    }

    /**
     * Обновление пароля текущим авторизованным пользователем.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // Проверка подтверждения
        ]);

        // Ищем пользователя по email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        // Проверяем текущий пароль
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Текущий пароль неверный'], 403);
        }

        // Обновляем пароль (мутатор автоматически выполнит хэширование)
        $user->password = $request->new_password;
        $user->save();

        return response()->json(['message' => 'Пароль успешно обновлён'], 200);
    }
}
