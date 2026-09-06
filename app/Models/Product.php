<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subtitle',
        'description',
        'price_bdt',
        'price_usd',
    ];

    protected $casts = [
        'price_bdt' => 'decimal:2',
        'price_usd' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
