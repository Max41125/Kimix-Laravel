<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParsingProgress extends Model
{
    protected $fillable = ['key', 'value'];
    public $timestamps = false; // Если не нужны временные метки
}
