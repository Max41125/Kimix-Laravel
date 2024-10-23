<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId; // Измените на правильное имя переменной, если нужно
    public $orderId; // Хранение orderId

    public function __construct($message, $userId, $orderId)
    {
        $this->message = $message;
        $this->userId = $userId; // Сохраните userId
        $this->orderId = $orderId; // Сохраните orderId
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private-chat.' . $this->orderId); // Убедитесь, что имя канала правильное
    }

    public function broadcastAs()
    {
        return 'MessageSent'; // Это имя события, на которое вы подписываетесь на фронтенде
    }

    public function broadcastWith()
    {   
        return [
            'user_id' => $this->userId, // Используйте userId вместо user_id
            'message' => $this->message,
        ];
    }
}
