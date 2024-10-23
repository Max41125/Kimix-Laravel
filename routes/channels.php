<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\Order;
use App\Models\User;
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
    // Проверьте, что пользователь имеет доступ к этому заказу
    return true; // или ваше условие авторизации
});