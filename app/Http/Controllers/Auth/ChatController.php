<?php 

namespace App\Http\Controllers\Auth;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'message' => 'required|string',
            'user_id' => 'required|integer',
            'order_id' => 'required|string',
            'username' => 'required|string',
        ]);

        $message = $request->input('message');
        $userId = $request->input('user_id');
        $orderId = $request->input('order_id'); // Get order_id from the request
        $username = $request->input('username'); // Get username from the request

        Log::info('Sending message', ['message' => $message, 'user_id' => $userId, 'order_id' => $orderId]);
        event(new MessageSent($message, $userId, $orderId, $username));

        return response()->json(['success' => true]);
    }

    public function getMessages($orderId)
    {
        // Получаем сообщения с загруженными данными о пользователях
        $messages = Message::with('user') // Загружаем связанные данные о пользователе
            ->where('order_id', $orderId)
            ->get();
    
        // Преобразуем коллекцию сообщений в массив с необходимыми полями
        $messagesWithUsernames = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'user_id' => $message->user_id,
                'username' => $message->user ? $message->user->name : null, // Получаем имя пользователя
                'order_id' => $message->order_id,
            ];
        });
    
        return response()->json($messagesWithUsernames); // Возвращаем сообщения в формате JSON
    }
    
}
