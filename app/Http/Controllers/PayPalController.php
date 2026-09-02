<?php

namespace App\Http\Controllers;

use App\Models\PayPalTransaction;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function __construct(protected PayPalService $paypal)
    {
    }

    /**
     * Show a simple form where the user enters an amount to test with,
     * plus a history of past transactions.
     */
    public function index()
    {
        $transactions = PayPalTransaction::latest()->take(20)->get();

        return view('paypal.index', compact('transactions'));
    }

    /**
     * Create the order and redirect the user to PayPal's approval page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->paypal->createOrder((float) $request->amount, $invoiceNumber);

        $approveUrl = collect($result['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (isset($result['error']) || !$approveUrl) {
            return back()->with('error', 'Order could not be created: ' . json_encode($result));
        }

        PayPalTransaction::create([
            'order_id'       => $result['id'],
            'invoice_number' => $invoiceNumber,
            'amount'         => $request->amount,
            'currency'       => config('paypal.currency'),
            'status'         => 'pending',
            'raw_response'   => $result,
        ]);

        return redirect()->away($approveUrl);
    }

    /**
     * PayPal redirects here after the buyer approves the order.
     */
    public function callback(Request $request)
    {
        $orderId = $request->query('token'); // PayPal sends the order id as "token"

        $transaction = PayPalTransaction::where('order_id', $orderId)->first();

        if (!$orderId) {
            return view('paypal.result', [
                'success'     => false,
                'message'     => 'Missing order token from PayPal.',
                'data'        => $request->all(),
                'transaction' => $transaction,
            ]);
        }

        $result = $this->paypal->captureOrder($orderId);

        $success = ($result['status'] ?? null) === 'COMPLETED';

        $capture = $result['purchase_units'][0]['payments']['captures'][0] ?? null;

        $transaction?->update([
            'status'        => $success ? 'success' : 'failed',
            'capture_id'    => $capture['id'] ?? null,
            'paypal_status' => $result['status'] ?? null,
            'payer_email'   => $result['payer']['email_address'] ?? null,
            'raw_response'  => $result,
        ]);

        return view('paypal.result', [
            'success'     => $success,
            'message'     => $success ? 'Payment completed successfully.' : 'Payment capture failed.',
            'data'        => $result,
            'transaction' => $transaction,
        ]);
    }

    /**
     * Buyer cancelled on PayPal's side.
     */
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
