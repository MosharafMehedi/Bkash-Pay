<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    protected $fillable = [
        'city', 'charge', 'free_above', 'estimated_days', 'is_active',
    ];

    protected $casts = [
        'charge'         => 'decimal:2',
        'free_above'     => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /**
     * Calculate final delivery charge for an order.
     */
    public function chargeFor(float $orderAmount): float
    {
        if ($this->free_above && $orderAmount >= (float) $this->free_above) {
            return 0;
        }
        return (float) $this->charge;
    }
}