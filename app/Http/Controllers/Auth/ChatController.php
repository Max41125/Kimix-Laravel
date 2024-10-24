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

    public function fetchMessages($orderId)
    {
        // Retrieve messages for the specified order ID
        $messages = Message::where('order_id', $orderId)->get();

        return response()->json($messages);
    }
}
