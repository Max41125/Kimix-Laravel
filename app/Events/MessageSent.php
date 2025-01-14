<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId; // Измените на правильное имя переменной, если нужно
    public $orderId; // Хранение orderId
    public $username;
    
    public function __construct($message, $userId, $orderId, $username)
    {

        Message::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'message' => $message,
        ]);

        $this->message = $message;
        $this->userId = $userId; // Сохраните userId
        $this->orderId = $orderId; // Сохраните orderId
        $this->username = $username;
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting on channel', ['channel' => 'chat.' . $this->orderId]);
        return [new Channel('chat.' . $this->orderId)]; // Убедитесь, что имя канала правильное
    }

    public function broadcastAs()
    {
        Log::info('Событие сработало');
        return 'messageSent'; // Это имя события, на которое вы подписываетесь на фронтенде
    }

}
