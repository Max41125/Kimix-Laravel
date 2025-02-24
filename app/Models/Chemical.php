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
        'russian_description',
        'inchi',
        'smiles',

    ];

    public function chemicalSynonyms()
    {
        return $this->hasMany(ChemicalSynonym::class, 'cid', 'cid');
    }
    
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'chemical_user');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'chemical_order')
                    ->withPivot('unit_type', 'price', 'currency', 'supplier_id', 'quantity', 'product_id')
                    ->withTimestamps()
                    ->join('chemical_user', 'chemical_order.product_id', '=', 'chemical_user.id') // соединяем с chemical_user по product_id
                    ->select('chemical_order.*', 'chemical_user.description as pivot_description');
    }


}
