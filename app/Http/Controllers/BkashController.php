<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\BkashTransaction;
use App\Services\BkashService;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BkashController extends Controller
{
    public function __construct(
        protected BkashService $bkash,
        protected CheckoutService $checkout,
        protected OrderService $orderService,
        protected CartService $cart,
    ) {}

    public function index()
    {
        $transactions = BkashTransaction::where('user_id', auth()->id())
            ->latest()
            ->take(20)
            ->get();

        return view('bkash.index', compact('transactions'));
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
            return redirect()->route('products.index')
                ->with('error', 'Your cart is empty.');
        }

        if ($request->input('order_type') === 'delivery') {
            $this->checkout->syncUserProfile($user, $request->all());
        }

        $summary = $this->checkout->resolveCart($user, $request->input('coupon_code'));

        if (! empty($summary['coupon_error'])) {
            return back()->with('error', $summary['coupon_error']);
        }

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

        $finalTotal = (float) $summary['total_bdt'] + $deliveryCharge;

        // Fully covered by wallet
        if ($finalTotal <= 0) {
            try {
                $order = $this->orderService->createFromCart(
                    $user,
                    'bkash',
                    null,
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
                    $user,
                    $order,
                    $summary['coupon'],
                    $summary['discount_bdt'],
                    $summary['balance_used_bdt'],
                    0
                );

                return redirect()->route('my-orders.show', $order)
                    ->with('success', 'Order placed!');
            } catch (\Throwable $e) {
                Log::error('bKash wallet-pay failed: ' . $e->getMessage());
                return back()->with('error', 'Could not complete order: ' . $e->getMessage());
            }
        }

        $invoiceNumber = 'INV-' . strtoupper(uniqid());
        $result = $this->bkash->createPayment($finalTotal, $invoiceNumber);

        if (isset($result['error']) || ! isset($result['bkashURL'])) {
            Log::error('bKash createPayment failed: ' . json_encode($result));
            return back()->with('error', 'Payment could not be created. Please try again.');
        }

        BkashTransaction::create([
            'user_id'         => $user->id,
            'product_id'      => null,
            'coupon_id'       => $summary['coupon']?->id,
            'payment_id'      => $result['paymentID'],
            'invoice_number'  => $invoiceNumber,
            'customer_email'  => $user->email,
            'amount'          => $finalTotal,
            'discount_amount' => $summary['discount_bdt'],
            'currency'        => 'BDT',
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

        return redirect()->away($result['bkashURL']);
    }

    public function callback(Request $request)
    {
        $paymentId = $request->query('paymentID');
        $status    = $request->query('status');

        $transaction = BkashTransaction::where('payment_id', $paymentId)->first();

        if ($status !== 'success' || ! $paymentId) {
            $transaction?->update([
                'status'             => $status === 'cancel' ? 'cancelled' : 'failed',
                'transaction_status' => $status,
            ]);

            return view('bkash.result', [
                'success'     => false,
                'message'     => 'Payment was cancelled or failed.',
                'data'        => $request->all(),
                'transaction' => $transaction,
            ]);
        }

        $result  = $this->bkash->executePayment($paymentId);
        $success = ($result['transactionStatus'] ?? null) === 'Completed';

        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $raw  = $transaction->raw_response ?? [];
                $user = $transaction->user;

                $order = $this->orderService->createFromCart(
                    $user,
                    'bkash',
                    $transaction->id,
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
                    $user,
                    $order,
                    $transaction->coupon,
                    (float) $transaction->discount_amount,
                    (float) ($raw['balance_used'] ?? 0),
                    0
                );

                $transaction->update(['order_id' => $order->id]);
            } catch (\Throwable $e) {
                Log::error('bKash order create failed: ' . $e->getMessage());
            }
        }

        $transaction?->update([
            'status'             => $success ? 'success' : 'failed',
            'trx_id'             => $result['trxID'] ?? null,
            'transaction_status' => $result['transactionStatus'] ?? null,
            'customer_msisdn'    => $result['customerMsisdn'] ?? null,
            'raw_response'       => array_merge($transaction->raw_response ?? [], $result),
        ]);

        if ($success && $transaction?->customer_email) {
            try {
                Mail::to($transaction->customer_email)
                    ->send(new PaymentReceiptMail($transaction, 'bKash', $transaction->trx_id));
            } catch (\Throwable $e) {
                Log::error('Mail failed: ' . $e->getMessage());
            }
        }

        return view('bkash.result', [
            'success'     => $success,
            'message'     => $success ? 'Payment completed successfully.' : 'Payment execution failed.',
            'data'        => $result,
            'transaction' => $transaction,
        ]);
    }

    public function status(string $paymentId)
    {
        return response()->json($this->bkash->queryPayment($paymentId));
    }
}