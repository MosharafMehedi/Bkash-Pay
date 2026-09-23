<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashOrder extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'coupon_id',
        'order_number',
        'otp',
        'otp_expires_at',
        'otp_attempts',
        'amount',
        'discount_amount',
        'currency',
        'customer_email',
        'status',
        'verified_at',
        'order_id',
    ];

    protected $casts = [
        'otp_expires_at'  => 'datetime',
        'verified_at'     => 'datetime',
        'amount'          => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function isExpired(): bool
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
}
