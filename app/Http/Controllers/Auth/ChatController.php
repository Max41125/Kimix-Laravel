<?php 

namespace App\Http\Controllers\Auth;

use App\Events\MessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Валидация входящих данных
        $request->validate([
            'message' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $message = $request->input('message');
        $userId = $request->input('user_id');

        // Вызываем событие
        broadcast(new MessageSent($message, $userId))->toOthers();

        return response()->json(['success' => true]);
    }
}
