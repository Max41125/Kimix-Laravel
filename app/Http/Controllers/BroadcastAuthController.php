<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        \Log::info('Custom Broadcast auth request:', $request->all());

        // Получаем результат аутентификации через Broadcast::auth
        $response = Broadcast::auth($request);

        // Проверим ответ
        \Log::info('Custom Broadcast auth response:', ['response' => $response]);

        // Если ответ пустой, значит проблема с авторизацией
        if (!$response) {
            return response()->json(['error' => 'Unauthorized'], 403); // Возвращаем ошибку, если не авторизован
        }

        return $response; // Возвращаем ответ, который ожидает Pusher
    }
}
