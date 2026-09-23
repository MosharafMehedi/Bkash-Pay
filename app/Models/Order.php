<?php

namespace App\Models;

use App\Models\Coupon;
use App\Models\DeliveryStatusLog;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'order_number', 'delivery_code',
        'delivery_code_expires_at', 'delivery_code_used_at', 'delivery_code_attempts',
        'source_type', 'source_id',
        'product_id', 'product_name', 'quantity',
        'subtotal', 'discount_amount', 'coupon_id', 'coupon_code',
        'delivery_charge', 'total_amount', 'currency',
        'order_type', 'delivery_method',
        'delivery_name', 'delivery_phone', 'delivery_address',
        'delivery_city', 'delivery_postal', 'delivery_note',
        'assigned_to', 'vendor_id', 'tracking_number',
        'status', 'payment_status',
        'confirmed_at', 'shipped_at', 'delivered_at',
        'cancelled_at', 'returned_at',
        'admin_note',
    ];

    protected $casts = [
        'delivery_code_expires_at' => 'datetime',
        'delivery_code_used_at'    => 'datetime',
        'confirmed_at'             => 'datetime',
        'shipped_at'               => 'datetime',
        'delivered_at'             => 'datetime',
        'cancelled_at'             => 'datetime',
        'returned_at'              => 'datetime',
        'subtotal'                 => 'decimal:2',
        'discount_amount'          => 'decimal:2',
        'delivery_charge'          => 'decimal:2',
        'total_amount'             => 'decimal:2',
    ];

    // ── Relations ──
    public function user()            { return $this->belongsTo(User::class); }
    public function product()         { return $this->belongsTo(Product::class); }
    public function coupon()          { return $this->belongsTo(Coupon::class); }
    public function deliveryMan()     { return $this->belongsTo(User::class, 'assigned_to'); }
    public function vendor()          { return $this->belongsTo(User::class, 'vendor_id'); }
    public function statusLogs()      { return $this->hasMany(DeliveryStatusLog::class)->latest(); }

    // ── Scopes ──
    public function scopeDelivery($q)          { return $q->where('order_type', 'delivery'); }
    public function scopePickup($q)            { return $q->where('order_type', 'pickup'); }
    public function scopeStatus($q, $status)   { return $q->where('status', $status); }
    public function scopeForDeliveryMan($q, $userId) {
        return $q->where('assigned_to', $userId);
    }
    public function scopeForVendor($q, $userId) {
        return $q->where('vendor_id', $userId);
    }

    // ── Helpers ──
    public function isPickup(): bool      { return $this->order_type === 'pickup'; }
    public function isDelivered(): bool   { return $this->status === 'delivered'; }
    public function isPickedUp(): bool    { return $this->status === 'picked_up'; }
    public function isCancelled(): bool   { return $this->status === 'cancelled'; }
    public function isPaid(): bool        { return $this->payment_status === 'paid'; }

    public function isDeliveryCodeExpired(): bool
    {
        return $this->delivery_code_expires_at && $this->delivery_code_expires_at->isPast();
    }

    public function isDeliveryCodeUsed(): bool
    {
        return $this->delivery_code_used_at !== null;
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'pending'          => ['label' => 'Pending',          'color' => '#fbbf24'],
            'processing'       => ['label' => 'Processing',       'color' => '#29e7ff'],
            'out_for_delivery' => ['label' => 'Out for Delivery', 'color' => '#a78bfa'],
            'delivered'        => ['label' => 'Delivered',        'color' => '#34d399'],
            'ready_for_pickup' => ['label' => 'Ready for Pickup', 'color' => '#29e7ff'],
            'picked_up'        => ['label' => 'Picked Up',        'color' => '#34d399'],
            'cancelled'        => ['label' => 'Cancelled',        'color' => '#f87171'],
            'returned'         => ['label' => 'Returned',         'color' => '#fb923c'],
            default            => ['label' => ucfirst($this->status), 'color' => '#94a3b8'],
        };
    }
}