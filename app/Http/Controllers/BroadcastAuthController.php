<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // Логирование запроса для отладки
        \Log::info('Custom Broadcast auth request:', $request->all());

        // Получение токена авторизации через Broadcast
        $response = Broadcast::auth($request);

        // Логирование ответа
        \Log::info('Custom Broadcast auth response:', ['response' => $response]);

        // Возвращаем корректный JSON ответ
        return response()->json($response);
    }
}
