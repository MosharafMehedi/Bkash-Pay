<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayPalTransaction extends Model
{
    protected $table = 'paypal_transactions';
    protected $fillable = [
        'order_id',
        'capture_id',
        'invoice_number',
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
}
