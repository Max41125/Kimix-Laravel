<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Illuminate\Support\Facades\Cookie;

class CustomCsrfCookieController extends CsrfCookieController
{
    /**
     * Return an empty response simply to trigger the storage of the CSRF cookie in the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        // Получаем CSRF токен из куки
        $token = $request->cookie('XSRF-TOKEN');

        // Если токен не установлен, генерируем новый
        if (!$token) {
            $token = csrf_token();
            $cookie = Cookie::make('XSRF-TOKEN', $token, 120, null, null, true, true);
        } else {
            $cookie = null; // Куки не нужно устанавливать, если токен уже существует
        }

        // Если запрос ожидает JSON, возвращаем ответ 200 с CSRF токеном
        if ($request->expectsJson()) {
            return (new JsonResponse(['csrfToken' => $token], 200))->withCookie($cookie);
        }

        // В противном случае возвращаем обычный ответ с кодом 200
        return (new Response(['message' => 'CSRF token set'], 200))->withCookie($cookie);
    }
}
