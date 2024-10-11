<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

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
        // Если запрос ожидает JSON, возвращаем ответ 200
        if ($request->expectsJson()) {
            return new JsonResponse(['message' => 'CSRF token set'], 200);
        }

        // В противном случае возвращаем обычный ответ с кодом 200
        return new Response(['message' => 'CSRF token set'], 200);
    }
}
