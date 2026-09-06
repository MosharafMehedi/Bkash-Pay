<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $gateway  'bKash' | 'PayPal' | 'SSLCommerz'
     * @param  string  $reference  the trx/capture/bank-trx id shown to the customer
     */
    public function __construct(
        public object $transaction,
        public string $gateway,
        public string $reference,
    ) {
    }

    public function build()
    {
        return $this->subject("Payment Receipt — {$this->transaction->invoice_number}")
            ->view('emails.payment-receipt')
            ->with([
                'transaction' => $this->transaction,
                'gateway'     => $this->gateway,
                'reference'   => $this->reference,
            ]);
    }
}
