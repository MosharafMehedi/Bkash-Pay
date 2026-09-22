<?php

namespace App\Http\Controllers;

use App\Mail\CashOrderOtpMail;
use App\Models\CashOrder;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CashController extends Controller
{
    public function __construct(protected CheckoutService $checkout)
    {
    }

    public function pay(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();

        if ($product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        $summary = $this->checkout->resolve($user, $product, $request->input('coupon_code'));

        if ($summary['coupon_error']) {
            return back()->with('error', $summary['coupon_error']);
        }

        $payableOnDelivery = max($summary['total_bdt'] - $summary['balance_used_bdt'], 0);

        // ✅ Save coupon_id + discount_amount in DB
        $order = CashOrder::create([
            'user_id'         => $user->id,
            'product_id'      => $product->id,
            'coupon_id'       => $summary['coupon']?->id,
            'order_number'    => 'COD-' . strtoupper(Str::random(8)),
            'amount'          => $payableOnDelivery,
            'discount_amount' => $summary['discount_bdt'],
            'currency'        => 'BDT',
            'customer_email'  => $user->email,
            'status'          => 'pending',
        ]);

        return redirect()->route('cash.confirm', $order);
    }

    public function confirm(CashOrder $order)
    {
        $this->authorizeOrder($order);
        if ($order->status !== 'pending') {
            return redirect()->route('products.index');
        }
        return view('cash.confirm', compact('order'));
    }

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

        // ✅ Settle using DB columns
        try {
            $this->checkout->settle(
                $order->user,
                $product,
                $order->coupon,
                (float) $order->discount_amount,
                (float) $order->amount,   // only what user pays on delivery (post-coupon, post-wallet)
                0,
                $order->order_number
            );
        } catch (\Throwable $e) {
            Log::error('COD settle failed: ' . $e->getMessage());
            return back()->with('error', 'Could not complete order: ' . $e->getMessage());
        }

        $order->update([
            'status'      => 'verified',
            'otp'         => null,
            'verified_at' => now(),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Order verified! Pay ৳' . number_format($order->amount, 0) . ' on delivery. Order #' . $order->order_number);
    }

    public function cancel(CashOrder $order)
    {
        $this->authorizeOrder($order);
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled', 'otp' => null]);
        }
        return redirect()->route('products.index')->with('success', 'Order cancelled.');
    }

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