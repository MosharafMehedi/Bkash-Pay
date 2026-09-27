<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // ── Relations ──
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Helpers ──

    /**
     * Line total (price × qty).
     */
    public function getLineTotalAttribute(): float
    {
        if (! $this->product) {
            return 0;
        }

        return (float) $this->product->final_price_bdt * $this->quantity;
    }

    /**
     * Unit price (for snapshot).
     */
    public function getUnitPriceAttribute(): float
    {
        return (float) ($this->product->final_price_bdt ?? 0);
    }
}