<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\Product;
use App\Models\SslCommerzTransaction;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SslCommerzController extends Controller
{
    public function __construct(protected SslCommerzService $sslcommerz)
    {
    }

    public function index()
    {
        $transactions = SslCommerzTransaction::where('user_id', auth()->id())->latest()->take(20)->get();

        return view('sslcommerz.index', compact('transactions'));
    }

    /**
     * Initiate the payment session for a specific product and redirect
     * the user to the SSLCommerz gateway page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();

        $tranId = 'TRX-' . strtoupper(uniqid());
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->sslcommerz->initiatePayment((float) $product->price_bdt, $tranId, [
            'name'  => $user->name,
            'email' => $user->email,
        ]);

        if (($result['status'] ?? null) !== 'SUCCESS' || !isset($result['GatewayPageURL'])) {
            return back()->with('error', 'Payment could not be initiated: ' . json_encode($result));
        }

        SslCommerzTransaction::create([
            'user_id'        => $user->id,
            'product_id'     => $product->id,
            'tran_id'        => $tranId,
            'invoice_number' => $invoiceNumber,
            'customer_email' => $user->email,
            'amount'         => $product->price_bdt,
            'currency'       => config('sslcommerz.currency'),
            'status'         => 'pending',
            'raw_response'   => $result,
        ]);

        return redirect()->away($result['GatewayPageURL']);
    }

    /**
     * SSLCommerz POSTs here after a successful payment. We must
     * re-validate with the Order Validation API before trusting it.
     */
    public function success(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');

        $transaction = SslCommerzTransaction::where('tran_id', $tranId)->first();

        $result = $this->sslcommerz->validateTransaction($valId);

        $success = in_array($result['status'] ?? null, ['VALID', 'VALIDATED']);

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

    /**
     * SSLCommerz POSTs here when a payment fails.
     */
    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');
        $transaction = SslCommerzTransaction::where('tran_id', $tranId)->first();

        $transaction?->update([
            'status'       => 'failed',
            'raw_response' => $request->all(),
        ]);

        return view('sslcommerz.result', [
            'success'     => false,
            'message'     => 'Payment failed.',
            'data'        => $request->all(),
            'transaction' => $transaction,
        ]);
    }

    /**
     * SSLCommerz POSTs here when the buyer cancels the payment.
     */
    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');
        $transaction = SslCommerzTransaction::where('tran_id', $tranId)->first();

        $transaction?->update([
            'status'       => 'cancelled',
            'raw_response' => $request->all(),
        ]);

        return view('sslcommerz.result', [
            'success'     => false,
            'message'     => 'Payment was cancelled.',
            'data'        => $request->all(),
            'transaction' => $transaction,
        ]);
    }

    /**
     * Optional: SSLCommerz can also POST an async IPN (Instant Payment
     * Notification) here — useful when the browser redirect doesn't
     * fire (e.g. user closes the tab). Re-validate before trusting it.
     */
    public function ipn(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');

        $transaction = SslCommerzTransaction::where('tran_id', $tranId)->first();

        $result = $this->sslcommerz->validateTransaction($valId);

        $success = in_array($result['status'] ?? null, ['VALID', 'VALIDATED']);

        $transaction?->update([
            'status'         => $success ? 'success' : 'failed',
            'val_id'         => $valId,
            'gateway_status' => $result['status'] ?? null,
            'raw_response'   => $result,
        ]);

        return response('OK', 200);
    }
}
