<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashOrder extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'order_number', 'otp', 'otp_expires_at',
        'otp_attempts', 'amount', 'currency', 'customer_email',
        'status', 'verified_at',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified_at'    => 'datetime',
        'amount'         => 'decimal:2',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }

    public function isExpired(): bool
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }
}