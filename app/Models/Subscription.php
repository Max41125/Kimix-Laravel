<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chemical_id', // Добавлено
        'type',
        'duration',
        'start_date',
        'end_date',
    ];

    protected $hidden = [
        'payment_id',
        'payment_status',
        'payment_amount',
        'payment_date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Добавлено отношение к веществу
    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }
}