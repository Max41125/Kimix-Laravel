<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, что пользователь аутентифицирован и является администратором
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request); // Если админ, продолжаем выполнение запроса
        }

        // Если не админ, редиректим на главную страницу
        return redirect('/');
    }
}
