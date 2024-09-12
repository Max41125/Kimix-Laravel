<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CasNumber extends Model
{
    use HasFactory;

    protected $fillable = ['cid', 'cas_number'];

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'cid', 'cid');
    }
}
