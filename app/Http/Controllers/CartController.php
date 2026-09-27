<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(protected CartService $cart)
    {
    }

    /**
     * Cart page.
     */
    public function index()
    {
        $user  = auth()->user();
        $items = $this->cart->getItems($user);

        $subtotal     = $this->cart->getSubtotal($user);
        $itemCount    = $this->cart->getCount($user);
        $uniqueCount  = $this->cart->getUniqueCount($user);

        return view('cart.index', compact('items', 'subtotal', 'itemCount', 'uniqueCount'));
    }

    /**
     * AJAX — Add to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:5',
        ]);

        $user    = $request->user();
        $product = Product::findOrFail($request->product_id);

        try {
            $item = $this->cart->add($user, $product, (int) $request->quantity);

            return response()->json([
                'ok'         => true,
                'message'    => 'Added to cart!',
                'cart_count' => $this->cart->getCount($user),
                'item'       => [
                    'id'       => $item->id,
                    'quantity' => $item->quantity,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'ok'      => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Cart add failed: ' . $e->getMessage());
            return response()->json([
                'ok'      => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * AJAX — Update cart item quantity.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        // Authorization
        abort_unless($cartItem->user_id === auth()->id(), 403);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:5',
        ]);

        try {
            $this->cart->update($cartItem, (int) $request->quantity);

            $user = auth()->user();

            return response()->json([
                'ok'         => true,
                'message'    => 'Quantity updated.',
                'cart_count' => $this->cart->getCount($user),
                'subtotal'   => $this->cart->getSubtotal($user),
                'item_total' => $cartItem->fresh()->line_total,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'ok'      => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Cart update failed: ' . $e->getMessage());
            return response()->json([
                'ok'      => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * AJAX — Remove cart item.
     */
    public function remove(CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);

        $this->cart->remove($cartItem);

        $user = auth()->user();

        return response()->json([
            'ok'         => true,
            'message'    => 'Item removed.',
            'cart_count' => $this->cart->getCount($user),
            'subtotal'   => $this->cart->getSubtotal($user),
        ]);
    }

    /**
     * AJAX — Clear cart.
     */
    public function clear()
    {
        $user = auth()->user();
        $this->cart->clear($user);

        return response()->json([
            'ok'         => true,
            'message'    => 'Cart cleared.',
            'cart_count' => 0,
            'subtotal'   => 0,
        ]);
    }

    /**
     * AJAX — Get cart count (for header badge).
     */
    public function count()
    {
        return response()->json([
            'count' => $this->cart->getCount(auth()->user()),
        ]);
    }
}