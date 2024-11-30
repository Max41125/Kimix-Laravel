<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    // Разрешенные для массового присвоения поля
    protected $fillable = [
        'user_id',
        'city',
        'street',
        'house',
        'building',
        'office',
        'phone',
        'inn',
        'buyer_fullname',
    ];
}
