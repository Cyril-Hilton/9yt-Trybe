<?php

namespace App\Mail;

use App\Models\EventPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventCongratulatoryEmail extends Mailable
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
            subject: 'Congratulations on Your Successful Event! 🎉',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $setupUrl = route('organization.payouts.setup', $this->payout);

        return new Content(
            markdown: 'emails.events.congratulatory',
            with: [
                'payout' => $this->payout,
                'event' => $this->payout->event,
                'company' => $this->payout->company,
                'setupUrl' => $setupUrl,
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
