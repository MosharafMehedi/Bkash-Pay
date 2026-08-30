<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_number',
        'payment_id',
        'trx_id',
        'amount',
        'status',
    ];
}
