<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\BkashTransaction;
use App\Models\Product;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BkashController extends Controller
{
    public function __construct(protected BkashService $bkash)
    {
    }

    public function index()
    {
        $transactions = BkashTransaction::where('user_id', auth()->id())->latest()->take(20)->get();

        return view('bkash.index', compact('transactions'));
    }

    public function pay(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();

        if ($product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->bkash->createPayment((float) $product->final_price_bdt, $invoiceNumber);

        if (isset($result['error']) || !isset($result['bkashURL'])) {
            return back()->with('error', 'Payment could not be created: ' . json_encode($result));
        }

        BkashTransaction::create([
            'user_id'        => $user->id,
            'product_id'     => $product->id,
            'payment_id'     => $result['paymentID'],
            'invoice_number' => $invoiceNumber,
            'customer_email' => $user->email,
            'amount'         => $product->final_price_bdt,
            'currency'       => 'BDT',
            'status'         => 'pending',
            'raw_response'   => $result,
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

        $result = $this->bkash->executePayment($paymentId);

        $success = ($result['transactionStatus'] ?? null) === 'Completed';

        // Decrement stock ONLY on success (and once)
        if ($success && $transaction && $transaction->status !== 'success') {
            DB::transaction(function () use ($transaction) {
                $product = Product::find($transaction->product_id);
                if ($product) {
                    $product->decrementStock();
                }
            });
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
        $result = $this->bkash->queryPayment($paymentId);

        return response()->json($result);
    }
}