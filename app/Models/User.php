<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'postal_code',
        'status',
        'avatar',
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
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('admin');
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

    /**
     * Get avatar URL or default.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && \Storage::disk('public')->exists($this->avatar)) {
            return \Storage::disk('public')->url($this->avatar);
        }

        return '';
    }

    /**
     * Check if user has avatar.
     */
    public function hasAvatar(): bool
    {
        return ! empty($this->avatar)
            && \Storage::disk('public')->exists($this->avatar);
    }

    /**
     * Get initials for default avatar.
     */
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
