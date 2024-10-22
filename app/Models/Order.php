<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'currency', 
    ];

    protected $casts = [
        'products' => 'array', // Автоматическое преобразование JSON в массив и обратно
    ];

    public function products()
    {
        return $this->belongsToMany(Chemical::class)->withPivot('unit_type', 'price', 'currency');
    }
    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
