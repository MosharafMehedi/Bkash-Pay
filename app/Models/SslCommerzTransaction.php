<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SslCommerzTransaction extends Model
{
    protected $table = 'sslcommerz_transactions';

    protected $fillable = [
        'user_id',
        'product_id',
        'tran_id',
        'val_id',
        'bank_tran_id',
        'card_type',
        'invoice_number',
        'customer_email',
        'amount',
        'currency',
        'status',
        'gateway_status',
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
