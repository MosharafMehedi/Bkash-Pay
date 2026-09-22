<?php

namespace App\Mail;

use App\Models\CashOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CashOrderOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CashOrder $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Cash on Delivery Verification Code — ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cash-order-otp',
            with: [
                'order' => $this->order,
            ],
        );
    }
}