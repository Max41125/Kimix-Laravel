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


class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId; // Измените на правильное имя переменной, если нужно
    public $orderId; // Хранение orderId
    public $username;

    public function __construct($message, $userId, $orderId)
    {
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

    public function broadcastWith()
    {   
        Log::info('Broadcasting with data', [
            'message' => $this->message,
            'user_id' => $this->userId,
            'order_id' => $this->orderId,
        ]);
    
        return [
            'message' => $this->message,
            'user_id' => $this->userId,
            'order_id' => $this->orderId,
        ];
    }
}
