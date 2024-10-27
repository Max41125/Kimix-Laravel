<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'filename',
        'path',
    ];

    // Связь с пользователем, который загрузил документ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
