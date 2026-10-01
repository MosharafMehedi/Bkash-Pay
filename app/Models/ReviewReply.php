<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'parent_id',
        'comment',
        'is_admin',
        'is_approved',
        'edited_at',
    ];

    protected $casts = [
        'is_admin'    => 'boolean',
        'is_approved' => 'boolean',
        'edited_at'   => 'datetime',
    ];

    // ── Relations ──
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ReviewReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ReviewReply::class, 'parent_id')->oldest();
    }

    // ── Scopes ──
    public function scopeApproved($q)
    {
        return $q->where('is_approved', true);
    }

    // ── Helpers ──
    public function isOwnedBy(?User $user): bool
    {
        return $user && $this->user_id === $user->id;
    }

    public function isEdited(): bool
    {
        return $this->edited_at !== null;
    }
}