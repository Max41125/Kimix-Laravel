<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;
    public $orderId;

    public function __construct($message, $userId, $orderId)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->orderId = $orderId; // Store orderId
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->orderId); 
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }

}
