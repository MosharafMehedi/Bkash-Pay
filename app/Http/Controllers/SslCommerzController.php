<?php

namespace App\Http\Controllers;

use App\Models\SslCommerzTransaction;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;

class SslCommerzController extends Controller
{
    public function __construct(protected SslCommerzService $sslcommerz)
    {
    }

    /**
     * Show a simple form where the user enters an amount to test with,
     * plus a history of past transactions.
     */
    public function index()
    {
        $transactions = SslCommerzTransaction::latest()->take(20)->get();

        return view('sslcommerz.index', compact('transactions'));
    }

    /**
     * Initiate the payment session and redirect the user to the
     * SSLCommerz gateway page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $tranId = 'TRX-' . strtoupper(uniqid());
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->sslcommerz->initiatePayment((float) $request->amount, $tranId);

        if (($result['status'] ?? null) !== 'SUCCESS' || !isset($result['GatewayPageURL'])) {
            return back()->with('error', 'Payment could not be initiated: ' . json_encode($result));
        }

        SslCommerzTransaction::create([
            'tran_id'        => $tranId,
            'invoice_number' => $invoiceNumber,
            'amount'         => $request->amount,
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
