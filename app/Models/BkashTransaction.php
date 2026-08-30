<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashTransaction extends Model
{
    protected $fillable = [
        'payment_id',
        'trx_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'transaction_status',
        'customer_msisdn',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount'       => 'decimal:2',
    ];
}
