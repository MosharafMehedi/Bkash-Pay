<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\SslCommerzTransaction;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SslCommerzController extends Controller
{
    public function __construct(
        protected SslCommerzService $sslcommerz,
        protected CheckoutService $checkout,
        protected OrderService $orderService,
        protected CartService $cart,
    ) {}

    public function index()
    {
        $transactions = SslCommerzTransaction::where('user_id', auth()->id())->latest()->take(20)->get();
        return view('sslcommerz.index', compact('transactions'));
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

        $finalTotal = (float) $summary['total_bdt'] + $deliveryCharge;

        if ($finalTotal <= 0) {
            try {
                $order = $this->orderService->createFromCart(
                    $user, 'sslcommerz', null,
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
                    $summary['discount_bdt'], $summary['balance_used_bdt'], 0
                );

                return redirect()->route('my-orders.show', $order)->with('success', 'Order placed!');
            } catch (\Throwable $e) {
                return back()->with('error', 'Could not complete order: ' . $e->getMessage());
            }
        }

        $tranId = 'TRX-' . strtoupper(uniqid());
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->sslcommerz->initiatePayment($finalTotal, $tranId, [
            'name'  => $user->name,
            'email' => $user->email,
        ]);

        if (($result['status'] ?? null) !== 'SUCCESS' || ! isset($result['GatewayPageURL'])) {
            return back()->with('error', 'Payment could not be initiated.');
        }

        SslCommerzTransaction::create([
            'user_id'         => $user->id,
            'product_id'      => null,
            'coupon_id'       => $summary['coupon']?->id,
            'tran_id'         => $tranId,
            'invoice_number'  => $invoiceNumber,
            'customer_email'  => $user->email,
            'amount'          => $finalTotal,
            'discount_amount' => $summary['discount_bdt'],
            'currency'        => config('sslcommerz.currency'),
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
                'balance_used'     => $summary['balance_used_bdt'],
            ]),
        ]);

        return redirect()->away($result['GatewayPageURL']);
    }

    public function success(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');

        $transaction = SslCommerzTransaction::where('tran_id', $tranId)->first();
        $result      = $this->sslcommerz->validateTransaction($valId);
        $success     = in_array($result['status'] ?? null, ['VALID', 'VALIDATED']);

        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $raw  = $transaction->raw_response ?? [];
                $user = $transaction->user;

                $order = $this->orderService->createFromCart(
                    $user, 'sslcommerz', $transaction->id,
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
                    (float) $transaction->discount_amount,
                    (float) ($raw['balance_used'] ?? 0), 0
                );

                $transaction->update(['order_id' => $order->id]);
            } catch (\Throwable $e) {
                Log::error('SSL order create failed: ' . $e->getMessage());
            }
        }

        $transaction?->update([
            'status'         => $success ? 'success' : 'failed',
            'val_id'         => $valId,
            'bank_tran_id'   => $result['bank_tran_id'] ?? null,
            'card_type'      => $result['card_type'] ?? null,
            'gateway_status' => $result['status'] ?? null,
            'raw_response'   => array_merge($transaction->raw_response ?? [], $result),
        ]);

        if ($success && $transaction?->customer_email) {
            try {
                Mail::to($transaction->customer_email)
                    ->send(new PaymentReceiptMail($transaction, 'SSLCommerz', $transaction->bank_tran_id));
            } catch (\Throwable $e) {
                Log::error('Mail failed: ' . $e->getMessage());
            }
        }

        return view('sslcommerz.result', [
            'success'     => $success,
            'message'     => $success ? 'Payment completed successfully.' : 'Payment could not be validated.',
            'data'        => $result,
            'transaction' => $transaction,
        ]);
    }

    public function fail(Request $request)
    {
        $transaction = SslCommerzTransaction::where('tran_id', $request->input('tran_id'))->first();
        $transaction?->update(['status' => 'failed', 'raw_response' => $request->all()]);

        return view('sslcommerz.result', [
            'success'     => false,
            'message'     => 'Payment failed.',
            'data'        => $request->all(),
            'transaction' => $transaction,
        ]);
    }

    public function cancel(Request $request)
    {
        $transaction = SslCommerzTransaction::where('tran_id', $request->input('tran_id'))->first();
        $transaction?->update(['status' => 'cancelled', 'raw_response' => $request->all()]);

        return view('sslcommerz.result', [
            'success'     => false,
            'message'     => 'Payment was cancelled.',
            'data'        => $request->all(),
            'transaction' => $transaction,
        ]);
    }

    public function ipn(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');

        $transaction = SslCommerzTransaction::where('tran_id', $tranId)->first();
        $result      = $this->sslcommerz->validateTransaction($valId);
        $success     = in_array($result['status'] ?? null, ['VALID', 'VALIDATED']);

        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $raw  = $transaction->raw_response ?? [];
                $user = $transaction->user;

                $order = $this->orderService->createFromCart(
                    $user, 'sslcommerz', $transaction->id,
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
                    (float) $transaction->discount_amount,
                    (float) ($raw['balance_used'] ?? 0), 0
                );

                $transaction->update(['order_id' => $order->id]);
            } catch (\Throwable $e) {
                Log::error('SSL IPN order create failed: ' . $e->getMessage());
            }
        }

        $transaction?->update([
            'status'         => $success ? 'success' : 'failed',
            'val_id'         => $valId,
            'gateway_status' => $result['status'] ?? null,
            'raw_response'   => array_merge($transaction->raw_response ?? [], $result),
        ]);

        return response('OK', 200);
    }
}