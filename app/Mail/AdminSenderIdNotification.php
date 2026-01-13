<?php

namespace App\Mail;

use App\Models\SmsSenderId;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class AdminSenderIdNotification extends Mailable
{
    use Queueable, SerializesModels;

    public SmsSenderId $senderId;

    public function __construct(SmsSenderId $senderId)
    {
        $this->senderId = $senderId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: 'New SMS Sender ID Request - ' . $this->senderId->sender_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-sender-id-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
