<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractOrder extends Model
{
    use HasFactory;

    // Таблица связана с этой моделью
    protected $table = 'contract_orders';

    // Поля, которые можно массово заполнять
    protected $fillable = [
        'order_id',
        'user_id',
        'language',
        'created_at',
        'updated_at',
    ];

    /**
     * Связь с моделью Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Связь с моделью User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
