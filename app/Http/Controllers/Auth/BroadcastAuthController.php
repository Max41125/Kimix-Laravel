<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BroadcastAuthController extends Controller
{
    public function authorize(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            Log::info('Пользователь аутентифицирован для канала', ['user' => $user]);
            return response()->json(['auth' => true]);
        }

        Log::warning('Не удалось аутентифицировать пользователя для канала');
        return response()->json(['auth' => false], 403);
    }
}
