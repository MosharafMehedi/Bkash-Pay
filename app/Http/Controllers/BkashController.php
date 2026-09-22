<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\BkashTransaction;
use App\Models\Product;
use App\Services\BkashService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BkashController extends Controller
{
    public function __construct(
        protected BkashService $bkash,
        protected CheckoutService $checkout,
    ) {
    }

    public function index()
    {
        $transactions = BkashTransaction::where('user_id', auth()->id())->latest()->take(20)->get();
        return view('bkash.index', compact('transactions'));
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

        // Fully paid by wallet
        if ($summary['payable_bdt'] <= 0) {
            $orderRef = 'INV-' . strtoupper(uniqid());
            try {
                $this->checkout->settle(
                    $user, $product, $summary['coupon'],
                    $summary['discount_bdt'], $summary['balance_used_bdt'], 0,
                    $orderRef
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'Could not complete order: ' . $e->getMessage());
            }
            return redirect()->route('products.index')
                ->with('success', 'Order paid with wallet balance! Ref ' . $orderRef);
        }

        $invoiceNumber = 'INV-' . strtoupper(uniqid());
        $result = $this->bkash->createPayment((float) $summary['payable_bdt'], $invoiceNumber);

        if (isset($result['error']) || !isset($result['bkashURL'])) {
            return back()->with('error', 'Payment could not be created: ' . json_encode($result));
        }

        // ✅ Save coupon_id + discount_amount in DB
        BkashTransaction::create([
            'user_id'         => $user->id,
            'product_id'      => $product->id,
            'coupon_id'       => $summary['coupon']?->id,
            'payment_id'      => $result['paymentID'],
            'invoice_number'  => $invoiceNumber,
            'customer_email'  => $user->email,
            'amount'          => $summary['payable_bdt'],
            'discount_amount' => $summary['discount_bdt'],
            'currency'        => 'BDT',
            'status'          => 'pending',
            'raw_response'    => $result,
        ]);

        return redirect()->away($result['bkashURL']);
    }

    public function callback(Request $request)
    {
        $paymentId = $request->query('paymentID');
        $status    = $request->query('status');

        $transaction = BkashTransaction::where('payment_id', $paymentId)->first();

        if ($status !== 'success' || !$paymentId) {
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

        // ✅ Settle using DB columns (not session)
        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $this->checkout->settle(
                    $transaction->user,
                    $transaction->product,
                    $transaction->coupon,                       // DB relation
                    (float) $transaction->discount_amount,      // DB value
                    0,
                    0,
                    $transaction->invoice_number
                );
            } catch (\Throwable $e) {
                Log::error('bKash settle failed: ' . $e->getMessage());
            }
        }

        $transaction?->update([
            'status'             => $success ? 'success' : 'failed',
            'trx_id'             => $result['trxID'] ?? null,
            'transaction_status' => $result['transactionStatus'] ?? null,
            'customer_msisdn'    => $result['customerMsisdn'] ?? null,
            'raw_response'       => $result,
        ]);

        if ($success && $transaction?->customer_email) {
            Mail::to($transaction->customer_email)
                ->send(new PaymentReceiptMail($transaction, 'bKash', $transaction->trx_id));
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