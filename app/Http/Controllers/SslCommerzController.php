<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\Product;
use App\Models\SslCommerzTransaction;
use App\Services\CheckoutService;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SslCommerzController extends Controller
{
    public function __construct(
        protected SslCommerzService $sslcommerz,
        protected CheckoutService $checkout,
    ) {
    }

    public function index()
    {
        $transactions = SslCommerzTransaction::where('user_id', auth()->id())->latest()->take(20)->get();
        return view('sslcommerz.index', compact('transactions'));
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

        $tranId        = 'TRX-' . strtoupper(uniqid());
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->sslcommerz->initiatePayment((float) $summary['payable_bdt'], $tranId, [
            'name'  => $user->name,
            'email' => $user->email,
        ]);

        if (($result['status'] ?? null) !== 'SUCCESS' || !isset($result['GatewayPageURL'])) {
            return back()->with('error', 'Payment could not be initiated: ' . json_encode($result));
        }

        // ✅ Save coupon_id + discount_amount in DB
        SslCommerzTransaction::create([
            'user_id'         => $user->id,
            'product_id'      => $product->id,
            'coupon_id'       => $summary['coupon']?->id,
            'tran_id'         => $tranId,
            'invoice_number'  => $invoiceNumber,
            'customer_email'  => $user->email,
            'amount'          => $summary['payable_bdt'],
            'discount_amount' => $summary['discount_bdt'],
            'currency'        => config('sslcommerz.currency'),
            'status'          => 'pending',
            'raw_response'    => $result,
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

        // ✅ Settle using DB columns
        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $this->checkout->settle(
                    $transaction->user,
                    $transaction->product,
                    $transaction->coupon,
                    (float) $transaction->discount_amount,
                    0,
                    0,
                    $transaction->invoice_number
                );
            } catch (\Throwable $e) {
                Log::error('SSL settle failed: ' . $e->getMessage());
            }
        }

        $transaction?->update([
            'status'         => $success ? 'success' : 'failed',
            'val_id'         => $valId,
            'bank_tran_id'   => $result['bank_tran_id'] ?? null,
            'card_type'      => $result['card_type'] ?? null,
            'gateway_status' => $result['status'] ?? null,
            'raw_response'   => $result,
        ]);

        if ($success && $transaction?->customer_email) {
            Mail::to($transaction->customer_email)
                ->send(new PaymentReceiptMail($transaction, 'SSLCommerz', $transaction->bank_tran_id));
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

        // ✅ Settle using DB columns
        if ($success && $transaction && $transaction->status !== 'success') {
            try {
                $this->checkout->settle(
                    $transaction->user,
                    $transaction->product,
                    $transaction->coupon,
                    (float) $transaction->discount_amount,
                    0,
                    0,
                    $transaction->invoice_number
                );
            } catch (\Throwable $e) {
                Log::error('SSL IPN settle failed: ' . $e->getMessage());
            }
        }

        $transaction?->update([
            'status'         => $success ? 'success' : 'failed',
            'val_id'         => $valId,
            'gateway_status' => $result['status'] ?? null,
            'raw_response'   => $result,
        ]);

        return response('OK', 200);
    }
}