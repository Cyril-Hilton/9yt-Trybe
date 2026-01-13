<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShopOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public ShopOrder $order;

    public function __construct(ShopOrder $order)
    {
        $this->order = $order->load('items.product');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛍️ Order Confirmation - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shop-order-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
