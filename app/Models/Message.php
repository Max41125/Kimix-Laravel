<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Указываем, какие поля могут быть массово заполнены
    protected $fillable = ['user_id', 'message', 'order_id'];


    // Определяем связь с моделью User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Указываем поле user_id
    }

    // Определяем связь с заказом, если требуется
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
