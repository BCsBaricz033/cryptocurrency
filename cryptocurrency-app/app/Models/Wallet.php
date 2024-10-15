<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'api_id',
        'name',
        'amount',
        'price',
        'total',
        'image',
        'price_change_24h',
        'price_change_percentage_24h',
    ];
    public $incrementing = false;
    
}
