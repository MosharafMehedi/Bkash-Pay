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
}
