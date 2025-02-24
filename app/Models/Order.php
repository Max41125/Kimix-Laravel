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
        'status',
    ];
    

    protected $casts = [
        'products' => 'array', // Автоматическое преобразование JSON в массив и обратно
    ];

    public function products()
    {
        return $this->belongsToMany(Chemical::class)->withPivot('unit_type', 'price', 'currency' , 'supplier_id', 'quantity', 'product_id',);
    }
    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static $statuses = [
        'new' => 'Новый заказ',
        'contract_verification' => 'Проверка контракта',
        'waiting_payment' => 'Ожидание оплаты',
        'packing' => 'Комплектация',
        'shipping' => 'Отгрузка',
        'shipped' => 'Отгружен',
    ];
    


}
