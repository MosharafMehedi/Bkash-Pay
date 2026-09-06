<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\PayPalTransaction;
use App\Models\Product;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PayPalController extends Controller
{
    public function __construct(protected PayPalService $paypal)
    {
    }

    public function index()
    {
        $transactions = PayPalTransaction::where('user_id', auth()->id())->latest()->take(20)->get();

        return view('paypal.index', compact('transactions'));
    }

    /**
     * Create the order for a specific product and redirect the user to
     * PayPal's approval page.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();

        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        $result = $this->paypal->createOrder((float) $product->price_usd, $invoiceNumber);

        $approveUrl = collect($result['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (isset($result['error']) || !$approveUrl) {
            return back()->with('error', 'Order could not be created: ' . json_encode($result));
        }

        PayPalTransaction::create([
            'user_id'        => $user->id,
            'product_id'     => $product->id,
            'order_id'       => $result['id'],
            'invoice_number' => $invoiceNumber,
            'customer_email' => $user->email,
            'amount'         => $product->price_usd,
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

        if ($success && $transaction?->customer_email) {
            Mail::to($transaction->customer_email)
                ->send(new PaymentReceiptMail($transaction, 'PayPal', $transaction->capture_id));
        }

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
