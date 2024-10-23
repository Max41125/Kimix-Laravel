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

 
Broadcast::channel('chat.{orderId}', function ($user, $orderId) {
    Log::info("User {$user->id} trying to subscribe to chat channel {$orderId}");
    return true; // или ваше условие авторизации
});