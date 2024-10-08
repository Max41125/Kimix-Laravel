<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'formula',
        'cid',
        'title',
        'cas_number',
        'description',
        'molecular_weight',
        'image',
        'russian_common_name',
        'russian_description'


    ];

    public function ChemicalSynonyms()
    {
        return $this->hasMany(CasNumber::class, 'cid', 'cid');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'chemical_user');
    }

}
