<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId; 
    public $orderId;

    public function __construct($message, $userId, $orderId)
    {
        Log::info('MessageSent event created', [
            'message' => $message,
            'userId' => $userId,
            'orderId' => $orderId,
        ]);
        
        $this->message = $message;
        $this->userId = $userId;
        $this->orderId = $orderId;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->orderId);
    }

    public function broadcastAs()
    {
        return 'messageSent';
    }

    public function broadcastWith()
    {   
        return [
            'message' => $this->message,
            'user_id' => $this->userId,
            'order_id' => $this->orderId,
        ];
    }
}
