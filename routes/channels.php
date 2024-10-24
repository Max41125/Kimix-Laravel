<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

// Канал для пользователей
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    Log::info("Attempting to authorize user channel: User {$user->id} for channel User {$id}");
    
    $isAuthorized = (int) $user->id === (int) $id;
    Log::info("Authorization result for User {$user->id}: " . ($isAuthorized ? 'Authorized' : 'Not Authorized'));
    
    return $isAuthorized;
});

// Приватный канал чата для заказов
Broadcast::channel('chat.{orderId}', function (User $user, $orderId) {

    
    return true;
}, ['guards' => ['web', 'auth']]);
