<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
        return new PrivateChannel('chat.' . $this->orderId); // Use orderId for the channel
    }
}
