<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public const MAX_PER_PRODUCT = 5;
    public const MAX_TOTAL_ITEMS = 15;

    /**
     * Get all cart items with product eager-loaded + stale items cleaned.
     */
    public function getItems(User $user): Collection
    {
        // Remove stale items (inactive / deleted products)
        $this->cleanStaleItems($user);

        return CartItem::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    /**
     * Add product to cart (or update qty if exists).
     */
    public function add(User $user, Product $product, int $quantity = 1): CartItem
    {
        // ── Validate product availability ──
        if (! $product->is_active) {
            throw ValidationException::withMessages([
                'cart' => 'This product is not available.',
            ]);
        }

        if ($product->stock <= 0) {
            throw ValidationException::withMessages([
                'cart' => 'This product is out of stock.',
            ]);
        }

        $quantity = max(1, (int) $quantity);

        return DB::transaction(function () use ($user, $product, $quantity) {
            $existing = CartItem::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            // ── Existing item → update quantity ──
            if ($existing) {
                $newQty = $existing->quantity + $quantity;

                if ($newQty > self::MAX_PER_PRODUCT) {
                    throw ValidationException::withMessages([
                        'cart' => 'You can add maximum ' . self::MAX_PER_PRODUCT . ' units of this product.',
                    ]);
                }

                if ($newQty > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Only {$product->stock} units available in stock.",
                    ]);
                }

                $existing->update(['quantity' => $newQty]);

                return $existing->fresh();
            }

            // ── New item → check total limit ──
            $currentTotal = CartItem::where('user_id', $user->id)->count();

            if ($currentTotal >= self::MAX_TOTAL_ITEMS) {
                throw ValidationException::withMessages([
                    'cart' => 'Cart can hold maximum ' . self::MAX_TOTAL_ITEMS . ' different products.',
                ]);
            }

            if ($quantity > self::MAX_PER_PRODUCT) {
                throw ValidationException::withMessages([
                    'cart' => 'You can add maximum ' . self::MAX_PER_PRODUCT . ' units of this product.',
                ]);
            }

            if ($quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'cart' => "Only {$product->stock} units available in stock.",
                ]);
            }

            return CartItem::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
                'quantity'   => $quantity,
            ]);
        });
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(CartItem $item, int $quantity): CartItem
    {
        $quantity = max(1, (int) $quantity);
        $product  = $item->product;

        if (! $product) {
            throw ValidationException::withMessages([
                'cart' => 'Product is no longer available.',
            ]);
        }

        if (! $product->is_active) {
            throw ValidationException::withMessages([
                'cart' => 'This product is not available.',
            ]);
        }

        if ($quantity > self::MAX_PER_PRODUCT) {
            throw ValidationException::withMessages([
                'cart' => 'You can add maximum ' . self::MAX_PER_PRODUCT . ' units of this product.',
            ]);
        }

        if ($quantity > $product->stock) {
            throw ValidationException::withMessages([
                'cart' => "Only {$product->stock} units available in stock.",
            ]);
        }

        $item->update(['quantity' => $quantity]);

        return $item->fresh();
    }

    /**
     * Remove a cart item.
     */
    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Clear entire cart.
     */
    public function clear(User $user): void
    {
        CartItem::where('user_id', $user->id)->delete();
    }

    /**
     * Cart total item count (sum of quantities).
     */
    public function getCount(User $user): int
    {
        return (int) CartItem::where('user_id', $user->id)->sum('quantity');
    }

    /**
     * Number of unique products.
     */
    public function getUniqueCount(User $user): int
    {
        return CartItem::where('user_id', $user->id)->count();
    }

    /**
     * Calculate subtotal (sum of line totals).
     */
    public function getSubtotal(User $user): float
    {
        $subtotal = 0;

        foreach ($this->getItems($user) as $item) {
            $subtotal += $item->line_total;
        }

        return round($subtotal, 2);
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(User $user): bool
    {
        return $this->getUniqueCount($user) === 0;
    }

    /**
     * Remove items where product is inactive or deleted.
     */
    protected function cleanStaleItems(User $user): void
    {
        CartItem::where('user_id', $user->id)
            ->whereDoesntHave('product', function ($q) {
                $q->where('is_active', true);
            })
            ->delete();
    }
}