<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\BkashTransaction;
use App\Models\Product;
use App\Services\BkashService;
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
        // ── 1. Validate ──
        $validated = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'coupon_code'      => 'nullable|string|max:50',
            'order_type'       => 'required|in:delivery,pickup',
            'delivery_name'    => 'required_if:order_type,delivery|nullable|string|max:255',
            'delivery_phone'   => 'required_if:order_type,delivery|nullable|string|max:20',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string',
            'delivery_city'    => 'required_if:order_type,delivery|nullable|string|max:100',
            'delivery_postal'  => 'nullable|string|max:20',
            'delivery_note'    => 'nullable|string',
            'delivery_charge'  => 'nullable|numeric|min:0',
        ]);

        // ── 2. Find product & user ──
        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();

        if ($product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        if ($request->input('order_type') === 'delivery') {
            $this->checkout->syncUserProfile($user, $request->all());
        }

        // ── 3. Resolve coupon + balance ──
        $summary = $this->checkout->resolve($user, $product, $request->input('coupon_code'));

        if (! empty($summary['coupon_error'])) {
            return back()->with('error', $summary['coupon_error']);
        }

        // ── 4. Calculate delivery charge (server-side, never trust client) ──
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

        // ── 5. If fully covered by wallet, skip gateway ──
        if ($finalTotal <= 0) {
            $orderRef = 'INV-' . strtoupper(uniqid());

            try {
                $order = $this->orderService->createFromTransaction([
                    'user_id'          => $user->id,
                    'source_type'      => 'bkash',
                    'source_id'        => null,
                    'product_id'       => $product->id,
                    'quantity'         => 1,
                    'subtotal'         => $summary['subtotal_bdt'],
                    'discount_amount'  => $summary['discount_bdt'],
                    'coupon_id'        => $summary['coupon']?->id,
                    'coupon_code'      => $summary['coupon_code'],
                    'delivery_charge'  => $deliveryCharge,
                    'total_amount'     => 0,
                    'currency'         => 'BDT',
                    'order_type'       => $orderType,
                    'delivery_method'  => $orderType === 'pickup' ? 'pickup' : 'self',
                    'delivery_name'    => $request->input('delivery_name', $user->name),
                    'delivery_phone'   => $request->input('delivery_phone', $user->phone),
                    'delivery_address' => $request->input('delivery_address'),
                    'delivery_city'    => $request->input('delivery_city'),
                    'delivery_postal'  => $request->input('delivery_postal'),
                    'delivery_note'    => $request->input('delivery_note'),
                    'payment_status'   => 'paid',
                ]);

                $this->checkout->settle(
                    $user,
                    $product,
                    $summary['coupon'],
                    $summary['discount_bdt'],
                    $summary['balance_used_bdt'],
                    0,
                    $orderRef
                );
            } catch (\Throwable $e) {
                Log::error('bKash wallet-pay failed: ' . $e->getMessage());
                return back()->with('error', 'Could not complete order: ' . $e->getMessage());
            }

            return redirect()->route('my-orders.show', $order)
                ->with('success', 'Order placed! Order # ' . $order->order_number);
        }

        // ── 6. Otherwise send to bKash gateway ──
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->bkash->createPayment($finalTotal, $invoiceNumber);

        if (isset($result['error']) || ! isset($result['bkashURL'])) {
            Log::error('bKash createPayment failed: ' . json_encode($result));
            return back()->with('error', 'Payment could not be created. Please try again.');
        }

        // ── 7. Save transaction with delivery context in raw_response ──
        BkashTransaction::create([
            'user_id'         => $user->id,
            'product_id'      => $product->id,
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
            ]),
        ]);

        // ── 8. Redirect to bKash ──
        return redirect()->away($result['bkashURL']);
    }

    public function callback(Request $request)
    {
        $paymentId = $request->query('paymentID');
        $status    = $request->query('status');

        $transaction = BkashTransaction::where('payment_id', $paymentId)->first();

        // ── Failed / cancel ──
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

        // ── Execute payment ──
        $result  = $this->bkash->executePayment($paymentId);
        $success = ($result['transactionStatus'] ?? null) === 'Completed';

        // ── Success: create order + settle ──
        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $raw  = $transaction->raw_response ?? [];
                $user = $transaction->user;

                // Create order
                $order = $this->orderService->createFromTransaction([
                    'user_id'          => $user->id,
                    'source_type'      => 'bkash',
                    'source_id'        => $transaction->id,
                    'product_id'       => $transaction->product_id,
                    'quantity'         => 1,
                    'subtotal'         => (float) ($raw['subtotal'] ?? ($transaction->amount + $transaction->discount_amount)),
                    'discount_amount'  => (float) $transaction->discount_amount,
                    'coupon_id'        => $transaction->coupon_id,
                    'coupon_code'      => $transaction->coupon?->code,
                    'delivery_charge'  => (float) ($raw['delivery_charge'] ?? 0),
                    'total_amount'     => (float) $transaction->amount,
                    'currency'         => 'BDT',
                    'order_type'       => $raw['order_type'] ?? 'delivery',
                    'delivery_method'  => ($raw['order_type'] ?? 'delivery') === 'pickup' ? 'pickup' : 'self',
                    'delivery_name'    => $raw['delivery_name'] ?? $user->name,
                    'delivery_phone'   => $raw['delivery_phone'] ?? $user->phone,
                    'delivery_address' => $raw['delivery_address'] ?? null,
                    'delivery_city'    => $raw['delivery_city'] ?? null,
                    'delivery_postal'  => $raw['delivery_postal'] ?? null,
                    'delivery_note'    => $raw['delivery_note'] ?? null,
                    'payment_status'   => 'paid',
                ]);

                // Settle stock + coupon + balance
                $this->checkout->settle(
                    $user,
                    $transaction->product,
                    $transaction->coupon,
                    (float) $transaction->discount_amount,
                    0,
                    0,
                    $transaction->invoice_number
                );

                // Link order to transaction
                $transaction->order_id = $order->id;
                $transaction->save();
            } catch (\Throwable $e) {
                Log::error('bKash order create failed: ' . $e->getMessage());
            }
        }

        // ── Update transaction ──
        $transaction?->update([
            'status'             => $success ? 'success' : 'failed',
            'trx_id'             => $result['trxID'] ?? null,
            'transaction_status' => $result['transactionStatus'] ?? null,
            'customer_msisdn'    => $result['customerMsisdn'] ?? null,
            'raw_response'       => array_merge($transaction->raw_response ?? [], $result),
        ]);

        // ── Mail receipt ──
        if ($success && $transaction?->customer_email) {
            try {
                Mail::to($transaction->customer_email)
                    ->send(new PaymentReceiptMail($transaction, 'bKash', $transaction->trx_id));
            } catch (\Throwable $e) {
                Log::error('bKash receipt mail failed: ' . $e->getMessage());
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
