<?php

namespace App\Http\Controllers;

use App\Models\BkashTransaction;
use App\Services\BkashService;
use Illuminate\Http\Request;

class BkashController extends Controller
{
    public function __construct(protected BkashService $bkash)
    {
    }

    /**
     * Show a simple form where the user enters an amount to test with,
     * plus a history of past transactions.
     */
    public function index()
    {
        $transactions = BkashTransaction::latest()->take(20)->get();

        return view('bkash.index', compact('transactions'));
    }

    /**
     * Create the payment and redirect the user to bKash's payment page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->bkash->createPayment((float) $request->amount, $invoiceNumber);

        if (isset($result['error']) || !isset($result['bkashURL'])) {
            return back()->with('error', 'Payment could not be created: ' . json_encode($result));
        }

        // Save a "pending" record now, we'll update it once the callback fires
        BkashTransaction::create([
            'payment_id'     => $result['paymentID'],
            'invoice_number' => $invoiceNumber,
            'amount'         => $request->amount,
            'currency'       => 'BDT',
            'status'         => 'pending',
            'raw_response'   => $result,
        ]);

        return redirect()->away($result['bkashURL']);
    }

    /**
     * bKash redirects the user back to this URL after they approve
     * (or cancel) the payment on their side.
     */
    public function callback(Request $request)
    {
        $paymentId = $request->query('paymentID');
        $status    = $request->query('status'); // success | failure | cancel

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

        $result = $this->bkash->executePayment($paymentId);

        $success = ($result['transactionStatus'] ?? null) === 'Completed';

        $transaction?->update([
            'status'             => $success ? 'success' : 'failed',
            'trx_id'             => $result['trxID'] ?? null,
            'transaction_status' => $result['transactionStatus'] ?? null,
            'customer_msisdn'    => $result['customerMsisdn'] ?? null,
            'raw_response'       => $result,
        ]);

        return view('bkash.result', [
            'success'     => $success,
            'message'     => $success ? 'Payment completed successfully.' : 'Payment execution failed.',
            'data'        => $result,
            'transaction' => $transaction,
        ]);
    }

    /**
     * Optional: manually check a transaction's status by paymentID.
     */
    public function status(string $paymentId)
    {
        $result = $this->bkash->queryPayment($paymentId);

        return response()->json($result);
    }
}
