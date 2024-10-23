<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

 
Broadcast::channel('private-chat.{orderId}', function ($user, $orderId) {
    \Log::info('Checking access for user ' . $user->id . ' to order ' . $orderId);
    $orderOwnerId = getOrderOwnerId($orderId);
    \Log::info('Order owner is: ' . $orderOwnerId);
    
    return (int) $user->id === (int) $orderOwnerId;
});
