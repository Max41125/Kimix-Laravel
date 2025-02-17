<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Убедитесь, что вы импортировали модель User
use Illuminate\Support\Facades\Hash; // Импортируйте Hash для проверки пароля

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
    
        // Получаем пользователя по email
        $user = User::where('email', $credentials['email'])->first();
    
        // Проверяем, существует ли пользователь и совпадает ли пароль
        if ($user && Hash::check($request->password, $user->password)) {
            Log::info('Пароли совпадают для пользователя:', ['email' => $credentials['email']]);
        } else {
            Log::warning('Пароли не совпадают или пользователь не найден:', $credentials);
            return response()->json(['message' => 'Неверный пароль или email'], 401);
        }
    
        // Логируем попытку входа
        Log::info('Attempting login for:', $credentials);
    
        // Устанавливаем продолжительность сессии в зависимости от флага "remember"
        $sessionDuration = $remember ? 60 * 24 * 30 : 60 * 24; // 30 дней или 1 день
    
        // Аутентификация пользователя с флагом "Remember me"
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
    
            // Генерация API токена
            $token = $user->generateToken();
    
            // Проверка на подтверждение email
            $verify = !is_null($user->email_verified_at);
    
            // Устанавливаем продолжительность сессии
            $cookie = cookie('remember_token', $token, $sessionDuration, null, null, false, false);

    
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'verify' => $verify,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ], 200)->cookie($cookie); // Возвращаем сессионный cookie
        }
    
        // Неверные учетные данные
        Log::warning('Invalid login attempt with credentials:', $credentials);
        return response()->json(['message' => 'Неверные учетные данные'], 401);
    }
    
    
    public function destroy(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Выход успешен'], 200);
    }

    public function sendVerificationEmail(Request $request)
    {
        // Проверяем, аутентифицирован ли пользователь
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Почта уже верифицирована.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Подтверждение отправлено на почту'], 200);
    }

    // Метод для отображения формы входа
    public function create()
    {
        // Возвращаем вид формы входа
        return view('auth.login');
    }



}
