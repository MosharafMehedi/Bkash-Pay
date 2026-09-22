<?php

namespace App\Http\Controllers;

use App\Mail\CashOrderOtpMail;
use App\Models\CashOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CashController extends Controller
{
    /**
     * Step 1: User clicks "Pay" with Cash on Delivery selected.
     * Creates a pending cash order and shows the confirm page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();

        if ($product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        $order = CashOrder::create([
            'user_id'        => $user->id,
            'product_id'     => $product->id,
            'order_number'   => 'COD-' . strtoupper(Str::random(8)),
            'amount'         => $product->final_price_bdt,
            'currency'       => 'BDT',
            'customer_email' => $user->email,
            'status'         => 'pending',
        ]);

        return redirect()->route('cash.confirm', $order);
    }

    /**
     * Step 2: Show the "Confirm / Cancel" page.
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
     * Step 3: User clicks "Confirm" → generate OTP, email it, show verify page.
     */
    public function sendOtp(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return redirect()->route('products.index');
        }

        // Generate new 6-digit OTP
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
     * Step 4: Show the OTP entry page.
     */
    public function verify(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status === 'verified') {
            return redirect()->route('products.index')
                ->with('success', 'Order already verified.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('products.index');
        }

        return view('cash.verify', compact('order'));
    }

    /**
     * Step 5: Match OTP. If correct → mark verified, decrement stock.
     */
    public function submitOtp(Request $request, CashOrder $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        if ($order->status !== 'pending') {
            return redirect()->route('products.index');
        }

        if ($order->isExpired()) {
            return back()->with('error', 'The code has expired. Please request a new one.');
        }

        // Prevent brute-force
        if ($order->otp_attempts >= 5) {
            return back()->with('error', 'Too many attempts. Please request a new code.');
        }

        // Match OTP
        if ($request->input('otp') !== $order->otp) {
            $order->increment('otp_attempts');

            return back()->with('error', 'Incorrect code. Please try again.');
        }

        // Success — verify order + decrement stock
        $product = $order->product;

        if (! $product || $product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is now out of stock.');
        }

        DB::transaction(function () use ($order, $product) {
            $decremented = $product->decrementStock();

            if (! $decremented) {
                throw new \RuntimeException('Stock not available');
            }

            $order->update([
                'status'      => 'verified',
                'otp'         => null,
                'verified_at' => now(),
            ]);
        });

        return redirect()->route('products.index')
            ->with('success', 'Order verified! Pay ৳' . number_format($order->amount, 0) . ' on delivery. Order #' . $order->order_number);
    }

    /**
     * Cancel the pending order.
     */
    public function cancel(CashOrder $order)
    {
        $this->authorizeOrder($order);

        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled', 'otp' => null]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Order cancelled.');
    }

    /**
     * Resend OTP (same as sendOtp, but keeps flow on verify page).
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

    /**
     * Ensure the order belongs to the logged-in user.
     */
    private function authorizeOrder(CashOrder $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
    }
}