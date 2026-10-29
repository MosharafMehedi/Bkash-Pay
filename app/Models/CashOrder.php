<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashOrder extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'coupon_id',
        'order_id',
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
        'raw_response',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'otp_expires_at'  => 'datetime',
        'verified_at'     => 'datetime',
        'raw_response'    => 'array',
        'amount'          => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'otp_attempts'    => 'integer',
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isExpired(): bool
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }
}