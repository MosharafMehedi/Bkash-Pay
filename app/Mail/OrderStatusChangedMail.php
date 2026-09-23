<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $newStatus)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Update — ' . $this->order->order_number . ' (' . ucfirst(str_replace('_', ' ', $this->newStatus)) . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-changed',
            with: [
                'order'     => $this->order,
                'newStatus' => $this->newStatus,
            ],
        );
    }
}