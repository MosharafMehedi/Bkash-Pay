<?php

namespace App\Models;
use App\Services\Traits\Auditable;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, Auditable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'postal_code',
        'avatar',
        'balance_bdt',
        'balance_usd',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'balance_bdt'       => 'decimal:2',
            'balance_usd'       => 'decimal:2',
            'is_active'         => 'boolean',
        ];
    }

    // ═══ Relations ═══

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'assigned_to');
    }

    public function vendorOrders()
    {
        return $this->hasMany(Order::class, 'vendor_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewReplies()
    {
        return $this->hasMany(ReviewReply::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ═══ Scopes ═══

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeInactive($q)
    {
        return $q->where('is_active', false);
    }

    // ═══ Helpers ═══

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return '';
    }

    public function hasAvatar(): bool
    {
        return ! empty($this->avatar)
            && Storage::disk('public')->exists($this->avatar);
    }

    public function getInitialsAttribute(): string
    {
        $names = explode(' ', trim($this->name ?? 'User'));
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
            if (strlen($initials) >= 2) break;
        }

        return $initials ?: 'U';
    }
}