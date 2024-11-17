<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'full_name',
        'short_name',
        'legal_address',
        'actual_address',
        'email',
        'phone',
        'inn',
        'kpp',
        'ogrn',
        'director',
        'chief_accountant',
        'authorized_person',
        'bank_name',
        'bik',
        'corr_account',
        'settlement_account',
        'okved',
        'tax_system',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
