<?php

namespace App\Http\Controllers;

use App\Mail\CashOrderOtpMail;
use App\Models\CashOrder;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CashController extends Controller
{
    public function __construct(
        protected CheckoutService $checkout,
        protected OrderService $orderService,
        protected CartService $cart,
    ) {}

    public function pay(Request $request)
    {
        $request->validate([
            'coupon_code'      => 'nullable|string|max:50',
            'order_type'       => 'required|in:delivery,pickup',
            'delivery_name'    => 'required_if:order_type,delivery|nullable|string|max:255',
            'delivery_phone'   => 'required_if:order_type,delivery|nullable|string|max:20',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string',
            'delivery_city'    => 'required_if:order_type,delivery|nullable|string|max:100',
            'delivery_postal'  => 'nullable|string|max:20',
            'delivery_note'    => 'nullable|string',
        ]);

        $user = $request->user();

        if ($this->cart->isEmpty($user)) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        if ($request->input('order_type') === 'delivery') {
            $this->checkout->syncUserProfile($user, $request->all());
        }

        $summary = $this->checkout->resolveCart($user, $request->input('coupon_code'));

        if (! empty($summary['coupon_error'])) {
            return back()->with('error', $summary['coupon_error']);
        }

        $orderType = $request->input('order_type');
        $deliveryCharge = 0;

        if ($orderType === 'delivery') {
            $chargeResult = $this->orderService->calculateDeliveryCharge(
                $request->input('delivery_city'),
                (float) $summary['total_bdt']
            );
            $deliveryCharge = (float) ($chargeResult['charge'] ?? 0);
        }

        $payableOnDelivery = max(
            (float) $summary['total_bdt'] + $deliveryCharge - (float) $summary['balance_used_bdt'],
            0
        );

        $order = CashOrder::create([
            'user_id'          => $user->id,
            'product_id'       => null,
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
            ->with('success', 'OTP sent to ' . $order->customer_email);
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
            return back()->with('error', 'Code expired. Request new one.');
        }
        if ($order->otp_attempts >= 5) {
            return back()->with('error', 'Too many attempts.');
        }
        if ($request->input('otp') !== $order->otp) {
            $order->increment('otp_attempts');
            return back()->with('error', 'Incorrect code.');
        }

        $user = $order->user;
        $raw  = $order->raw_response ?? [];

        try {
            $realOrder = $this->orderService->createFromCart(
                $user, 'cash', $order->id,
                [
                    'order_type'       => $raw['order_type'] ?? 'delivery',
                    'delivery_method'  => ($raw['order_type'] ?? 'delivery') === 'pickup' ? 'pickup' : 'self',
                    'delivery_name'    => $raw['delivery_name'] ?? $user->name,
                    'delivery_phone'   => $raw['delivery_phone'] ?? $user->phone,
                    'delivery_address' => $raw['delivery_address'] ?? null,
                    'delivery_city'    => $raw['delivery_city'] ?? null,
                    'delivery_postal'  => $raw['delivery_postal'] ?? null,
                    'delivery_note'    => $raw['delivery_note'] ?? null,
                ],
                'unpaid',
                (float) ($raw['delivery_charge'] ?? 0),
                (float) $order->discount_amount,
                $order->coupon_id,
                $order->coupon?->code
            );

            $this->orderService->settleCart(
                $user, $realOrder, $order->coupon,
                (float) $order->discount_amount,
                (float) ($raw['balance_used'] ?? 0), 0
            );

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
            return back()->with('error', 'Could not complete: ' . $e->getMessage());
        }
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

        return back()->with('success', 'New code sent.');
    }

    private function authorizeOrder(CashOrder $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
    }
}