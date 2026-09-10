<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayPalTransaction extends Model
{
    protected $table = 'paypal_transactions';
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'capture_id',
        'invoice_number',
        'customer_email',
        'amount',
        'currency',
        'status',
        'paypal_status',
        'payer_email',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
