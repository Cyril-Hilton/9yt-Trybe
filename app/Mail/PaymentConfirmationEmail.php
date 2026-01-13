<?php

namespace App\Mail;

use App\Models\EventPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public EventPayout $payout
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Processed - {$this->payout->formatted_net_amount} Sent! ✅",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $detailsUrl = route('organization.payouts.show', $this->payout);

        return new Content(
            markdown: 'emails.events.payment-confirmation',
            with: [
                'payout' => $this->payout,
                'event' => $this->payout->event,
                'company' => $this->payout->company,
                'paymentAccount' => $this->payout->paymentAccount,
                'detailsUrl' => $detailsUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
