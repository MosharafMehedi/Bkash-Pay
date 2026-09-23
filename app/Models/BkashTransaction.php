<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashTransaction extends Model
{
    protected $table = 'bkash_transactions';
    protected $fillable = [
        'user_id',
        'product_id',
        'payment_id',
        'trx_id',
        'invoice_number',
        'customer_email',
        'amount',
        'currency',
        'status',
        'transaction_status',
        'customer_msisdn',
        'raw_response',
        'discount_amount',
        'coupon_id',
        'order_id',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount'       => 'decimal:2',
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
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
}
