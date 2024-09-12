<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalSynonym extends Model
{
    use HasFactory;

    protected $fillable = ['cid', 'name'];

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'cid', 'cid');
    }
}
