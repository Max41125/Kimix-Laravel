<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalSynonym extends Model
{
    use HasFactory;

    protected $fillable = ['cid', 'name', 'russian_name'];

    public function chemical()
    {
        return $this->belongsTo(Chemical::class, 'cid', 'cid');
    }
    
}
