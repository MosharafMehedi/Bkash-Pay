<?php

namespace App\Models;

use App\Models\CouponUsage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'description',
        'type', 'value', 'min_order', 'max_discount',
        'usage_limit', 'used_count', 'per_user_limit',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
        'is_active'    => 'boolean',
        'value'        => 'decimal:2',
        'min_order'    => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function discountFor(float $subtotal): float
    {
        if ($this->type === 'percent') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount) {
                $discount = min($discount, (float) $this->max_discount);
            }
            return round($discount, 2);
        }

        // Fixed
        return min((float) $this->value, $subtotal);
    }

    public function isValid(): array
    {
        if (! $this->is_active) {
            return [false, 'This coupon is inactive.'];
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return [false, 'This coupon is not active yet.'];
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return [false, 'This coupon has expired.'];
        }
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return [false, 'This coupon has reached its usage limit.'];
        }
        return [true, null];
    }

    public function hasUserReachedLimit(int $userId): bool
    {
        if (! $this->per_user_limit) {
            return false;
        }
        $used = $this->usages()->where('user_id', $userId)->count();
        return $used >= $this->per_user_limit;
    }
    
}