<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;
use App\Models\User;

// Канал для пользователей (пример трансляции личных уведомлений)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Приватный канал чата для заказов
Broadcast::channel('private-chat.{orderId}', function (User $user, int $orderId) {
    // Найдем заказ и проверим, является ли пользователь владельцем заказа
    return $user->id === Order::findOrNew($orderId)->user_id;
});
