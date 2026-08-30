<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class BkashPaymentController extends Controller
{
    public function __construct(protected BkashService $bkash) {}

    /**
     * Show a simple form where the user enters an amount to test with.
     */
    public function index()
    {
        return view('bkash.index');
    }

    /**
     * Create the payment and redirect the user to bKash's payment page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $result = $this->bkash->createPayment((float) $request->amount);

        if (isset($result['error']) || !isset($result['bkashURL'])) {
            return back()->with('error', 'Payment could not be created: ' . json_encode($result));
        }

        // Save paymentID in session in case you need it before the callback
        session(['bkash_payment_id' => $result['paymentID']]);

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

        if ($status !== 'success' || !$paymentId) {
            return view('bkash.result', [
                'success' => false,
                'message' => 'Payment was cancelled or failed.',
                'data'    => $request->all(),
            ]);
        }

        $result = $this->bkash->executePayment($paymentId);

        $success = ($result['transactionStatus'] ?? null) === 'Completed';

        return view('bkash.result', [
            'success' => $success,
            'message' => $success ? 'Payment completed successfully.' : 'Payment execution failed.',
            'data'    => $result,
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
