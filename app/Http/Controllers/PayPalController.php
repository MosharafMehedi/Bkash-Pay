<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\PayPalTransaction;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayPalController extends Controller
{
    public function __construct(
        protected PayPalService $paypal,
        protected CheckoutService $checkout,
        protected OrderService $orderService,
        protected CartService $cart,
    ) {}

    public function index()
    {
        $transactions = PayPalTransaction::where('user_id', auth()->id())->latest()->take(20)->get();
        return view('paypal.index', compact('transactions'));
    }

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

        $finalTotalUsd = (float) $summary['payable_usd']; // USD for PayPal

        if ($finalTotalUsd <= 0) {
            try {
                $order = $this->orderService->createFromCart(
                    $user, 'paypal', null,
                    [
                        'order_type'       => $orderType,
                        'delivery_method'  => $orderType === 'pickup' ? 'pickup' : 'self',
                        'delivery_name'    => $request->input('delivery_name', $user->name),
                        'delivery_phone'   => $request->input('delivery_phone', $user->phone),
                        'delivery_address' => $request->input('delivery_address'),
                        'delivery_city'    => $request->input('delivery_city'),
                        'delivery_postal'  => $request->input('delivery_postal'),
                        'delivery_note'    => $request->input('delivery_note'),
                    ],
                    'paid',
                    $deliveryCharge,
                    $summary['discount_bdt'],
                    $summary['coupon']?->id,
                    $summary['coupon_code']
                );

                $this->orderService->settleCart(
                    $user, $order, $summary['coupon'],
                    $summary['discount_bdt'], 0, $summary['balance_used_usd']
                );

                return redirect()->route('my-orders.show', $order)->with('success', 'Order placed!');
            } catch (\Throwable $e) {
                return back()->with('error', 'Could not complete order: ' . $e->getMessage());
            }
        }

        $invoiceNumber = 'INV-' . strtoupper(uniqid());
        $result = $this->paypal->createOrder($finalTotalUsd, $invoiceNumber);

        $approveUrl = collect($result['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        if (isset($result['error']) || ! $approveUrl) {
            return back()->with('error', 'Order could not be created.');
        }

        PayPalTransaction::create([
            'user_id'         => $user->id,
            'product_id'      => null,
            'coupon_id'       => $summary['coupon']?->id,
            'order_id'        => $result['id'],
            'invoice_number'  => $invoiceNumber,
            'customer_email'  => $user->email,
            'amount'          => $finalTotalUsd,
            'discount_amount' => $summary['discount_bdt'],
            'currency'        => config('paypal.currency'),
            'status'          => 'pending',
            'raw_response'    => array_merge($result, [
                'order_type'       => $orderType,
                'delivery_name'    => $request->input('delivery_name', $user->name),
                'delivery_phone'   => $request->input('delivery_phone', $user->phone),
                'delivery_address' => $request->input('delivery_address'),
                'delivery_city'    => $request->input('delivery_city'),
                'delivery_postal'  => $request->input('delivery_postal'),
                'delivery_note'    => $request->input('delivery_note'),
                'delivery_charge'  => $deliveryCharge,
                'subtotal'         => $summary['subtotal_bdt'],
                'balance_used'     => $summary['balance_used_usd'],
            ]),
        ]);

        return redirect()->away($approveUrl);
    }

    public function callback(Request $request)
    {
        $orderId     = $request->query('token');
        $transaction = PayPalTransaction::where('order_id', $orderId)->first();

        if (! $orderId) {
            return view('paypal.result', [
                'success'     => false,
                'message'     => 'Missing order token.',
                'data'        => $request->all(),
                'transaction' => $transaction,
            ]);
        }

        $result  = $this->paypal->captureOrder($orderId);
        $success = ($result['status'] ?? null) === 'COMPLETED';
        $capture = $result['purchase_units'][0]['payments']['captures'][0] ?? null;

        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $raw  = $transaction->raw_response ?? [];
                $user = $transaction->user;

                $order = $this->orderService->createFromCart(
                    $user, 'paypal', $transaction->id,
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
                    'paid',
                    (float) ($raw['delivery_charge'] ?? 0),
                    (float) $transaction->discount_amount,
                    $transaction->coupon_id,
                    $transaction->coupon?->code
                );

                $this->orderService->settleCart(
                    $user, $order, $transaction->coupon,
                    (float) $transaction->discount_amount, 0,
                    (float) ($raw['balance_used'] ?? 0)
                );

                $transaction->update(['order_id' => $order->id]);
            } catch (\Throwable $e) {
                Log::error('PayPal order create failed: ' . $e->getMessage());
            }
        }

        $transaction?->update([
            'status'        => $success ? 'success' : 'failed',
            'capture_id'    => $capture['id'] ?? null,
            'paypal_status' => $result['status'] ?? null,
            'payer_email'   => $result['payer']['email_address'] ?? null,
            'raw_response'  => $result,
        ]);

        if ($success && $transaction?->customer_email) {
            try {
                Mail::to($transaction->customer_email)
                    ->send(new PaymentReceiptMail($transaction, 'PayPal', $transaction->capture_id));
            } catch (\Throwable $e) {
                Log::error('Mail failed: ' . $e->getMessage());
            }
        }

        return view('paypal.result', [
            'success'     => $success,
            'message'     => $success ? 'Payment completed successfully.' : 'Payment capture failed.',
            'data'        => $result,
            'transaction' => $transaction,
        ]);
    }

    public function cancel(Request $request)
    {
        $orderId = $request->query('token');
        $transaction = PayPalTransaction::where('order_id', $orderId)->first();
        $transaction?->update(['status' => 'cancelled']);

        return view('paypal.result', [
            'success'     => false,
            'message'     => 'Payment was cancelled.',
            'data'        => $request->all(),
            'transaction' => $transaction,
        ]);
    }
}