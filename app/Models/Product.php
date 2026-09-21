<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'subtitle', 'description',
        'price_bdt', 'price_usd', 'discount_price',
        'image', 'gallery',
        'quantity', 'stock', 'sku',
        'category', 'brand', 'tags',
        'rating', 'review_count',
        'is_active', 'is_featured', 'published_at',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'gallery'      => 'array',
        'tags'         => 'array',
        'is_active'    => 'boolean',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
        'rating'       => 'decimal:2',
    ];

    // Auto slug
    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(4);
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });
    }

    // Scopes
    public function scopeActive($q)        { return $q->where('is_active', true); }
    public function scopeFeatured($q)      { return $q->where('is_featured', true); }
    public function scopeInStock($q)       { return $q->where('stock', '>', 0); }

    // Helpers
    public function getFinalPriceBdtAttribute(): float
    {
        return $this->discount_price ?? $this->price_bdt;
    }

    public function getStockBadgeAttribute(): array
    {
        if ($this->stock <= 0)   return ['label' => 'Out of stock', 'class' => 'stock-out'];
        if ($this->stock <= 3)   return ['label' => "Only {$this->stock} left", 'class' => 'stock-low'];
        return ['label' => 'In stock', 'class' => 'stock-ok'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}