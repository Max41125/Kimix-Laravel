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

 

Broadcast::channel('private-chat.{orderId}', function (User $user, int $orderId) {
    \Log::info('Авторизация для пользователя', ['user_id' => $user->id, 'orderId' => $orderId]);

    $order = Order::find($orderId);

    if (!$order) {
        \Log::info('Заказ не найден', ['orderId' => $orderId]);
        return false;
    }

    $authorized = $user->id === $order->user_id;

    \Log::info('Результат авторизации', ['authorized' => $authorized]);

    return $authorized;
});