<?php

namespace App\Http\Controllers;

use App\Mail\CashOrderOtpMail;
use App\Models\CashOrder;
use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CashController extends Controller
{
    public function __construct(
        protected CheckoutService $checkout,
        protected OrderService $orderService,
    ) {
    }

    /**
     * Step 1: Create pending CashOrder + redirect to confirm page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'product_id'       => 'required|exists:products,id',
            'coupon_code'      => 'nullable|string|max:50',
            'order_type'       => 'required|in:delivery,pickup',
            'delivery_name'    => 'required_if:order_type,delivery|nullable|string|max:255',
            'delivery_phone'   => 'required_if:order_type,delivery|nullable|string|max:20',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string',
            'delivery_city'    => 'required_if:order_type,delivery|nullable|string|max:100',
            'delivery_postal'  => 'nullable|string|max:20',
            'delivery_note'    => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();

        if ($product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        // ── Resolve coupon + balance ──
        $summary = $this->checkout->resolve($user, $product, $request->input('coupon_code'));

        if (! empty($summary['coupon_error'])) {
            return back()->with('error', $summary['coupon_error']);
        }

        // ── Delivery charge (server-side) ──
        $orderType = $request->input('order_type');

        if ($orderType === 'delivery') {
            $chargeResult   = $this->orderService->calculateDeliveryCharge(
                $request->input('delivery_city'),
                (float) $summary['total_bdt']
            );
            $deliveryCharge = (float) ($chargeResult['charge'] ?? 0);
        } else {
            $deliveryCharge = 0;
        }

        $payableOnDelivery = max(
            (float) $summary['total_bdt'] + $deliveryCharge - (float) $summary['balance_used_bdt'],
            0
        );

        // ── Create CashOrder (holds OTP context until verified) ──
        $order = CashOrder::create([
            'user_id'          => $user->id,
            'product_id'       => $product->id,
            'coupon_id'        => $summary['coupon']?->id,
            'order_number'     => 'COD-' . strtoupper(Str::random(8)),
            'amount'           => $payableOnDelivery,
            'discount_amount'  => $summary['discount_bdt'],
            'currency'         => 'BDT',
            'customer_email'   => $user->email,
            'status'           => 'pending',
            'raw_response'     => [
                'order_type'       => $orderType,
                'delivery_name'    => $request->input('delivery_name', $user->name),
                'delivery_phone'   => $request->input('delivery_phone', $user->phone),
                'delivery_address' => $request->input('delivery_address'),
                'delivery_city'    => $request->input('delivery_city'),
                'delivery_postal'  => $request->input('delivery_postal'),
                'delivery_note'    => $request->input('delivery_note'),
                'delivery_charge'  => $deliveryCharge,
                'subtotal'         => $summary['subtotal_bdt'],
                'balance_used'     => $summary['balance_used_bdt'],
            ],
        ]);

        return redirect()->route('cash.confirm', $order);
    }

    /**
     * Step 2: Confirm/Cancel page.
     */
    public function confirm(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return redirect()->route('products.index');
        }

        return view('cash.confirm', compact('order'));
    }

    /**
     * Step 3: Generate OTP + email it.
     */
    public function sendOtp(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return redirect()->route('products.index');
        }

        $order->update([
            'otp'            => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts'   => 0,
        ]);

        Mail::to($order->customer_email)->send(new CashOrderOtpMail($order));

        return redirect()->route('cash.verify', $order)
            ->with('success', 'A verification code has been sent to ' . $order->customer_email);
    }

    /**
     * Step 4: Verify page.
     */
    public function verify(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status === 'verified') {
            return redirect()->route('products.index')->with('success', 'Order already verified.');
        }
        if ($order->status === 'cancelled') {
            return redirect()->route('products.index');
        }

        return view('cash.verify', compact('order'));
    }

    /**
     * Step 5: Submit OTP → create real Order + settle stock.
     */
    public function submitOtp(Request $request, CashOrder $order)
    {
        $this->authorizeOrder($order);

        $request->validate(['otp' => 'required|digits:6']);

        if ($order->status !== 'pending') {
            return redirect()->route('products.index');
        }
        if ($order->isExpired()) {
            return back()->with('error', 'The code has expired. Please request a new one.');
        }
        if ($order->otp_attempts >= 5) {
            return back()->with('error', 'Too many attempts. Please request a new code.');
        }
        if ($request->input('otp') !== $order->otp) {
            $order->increment('otp_attempts');
            return back()->with('error', 'Incorrect code. Please try again.');
        }

        $product = $order->product;
        if (! $product || $product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is now out of stock.');
        }

        $raw = $order->raw_response ?? [];
        $user = $order->user;

        try {
            // ── Create common Order ──
            $realOrder = $this->orderService->createFromTransaction([
                'user_id'          => $user->id,
                'source_type'      => 'cash',
                'source_id'        => $order->id,
                'product_id'       => $order->product_id,
                'quantity'         => 1,
                'subtotal'         => (float) ($raw['subtotal'] ?? $order->amount),
                'discount_amount'  => (float) $order->discount_amount,
                'coupon_id'        => $order->coupon_id,
                'coupon_code'      => $order->coupon?->code,
                'delivery_charge'  => (float) ($raw['delivery_charge'] ?? 0),
                'total_amount'     => (float) $order->amount,
                'currency'         => 'BDT',
                'order_type'       => $raw['order_type'] ?? 'delivery',
                'delivery_method'  => ($raw['order_type'] ?? 'delivery') === 'pickup' ? 'pickup' : 'self',
                'delivery_name'    => $raw['delivery_name'] ?? $user->name,
                'delivery_phone'   => $raw['delivery_phone'] ?? $user->phone,
                'delivery_address' => $raw['delivery_address'] ?? null,
                'delivery_city'    => $raw['delivery_city'] ?? null,
                'delivery_postal'  => $raw['delivery_postal'] ?? null,
                'delivery_note'    => $raw['delivery_note'] ?? null,
                'payment_status'   => 'unpaid',   // COD → paid on delivery
            ]);

            // ── Settle stock + coupon + balance ──
            $this->checkout->settle(
                $user,
                $product,
                $order->coupon,
                (float) $order->discount_amount,
                (float) ($raw['balance_used'] ?? 0),
                0,
                $order->order_number
            );

            // ── Mark CashOrder as verified + link to real order ──
            $order->update([
                'status'      => 'verified',
                'otp'         => null,
                'verified_at' => now(),
                'order_id'    => $realOrder->id,
            ]);

            return redirect()->route('my-orders.show', $realOrder)
                ->with('success', 'Order placed! Pay ৳' . number_format($order->amount, 0) . ' on delivery.');
        } catch (\Throwable $e) {
            Log::error('COD settle failed: ' . $e->getMessage());
            return back()->with('error', 'Could not complete order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel pending CashOrder.
     */
    public function cancel(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled', 'otp' => null]);
        }

        return redirect()->route('products.index')->with('success', 'Order cancelled.');
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return redirect()->route('products.index');
        }

        $order->update([
            'otp'            => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts'   => 0,
        ]);

        Mail::to($order->customer_email)->send(new CashOrderOtpMail($order));

        return back()->with('success', 'A new code has been sent to your email.');
    }

    private function authorizeOrder(CashOrder $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
    }
}