<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryStatusLog extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'note',
        'created_by',
        'updated_by',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}