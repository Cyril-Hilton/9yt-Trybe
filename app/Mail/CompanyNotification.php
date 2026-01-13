<?php

namespace App\Mail;

use App\Models\Conference;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class CompanyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Registration $registration;
    public Conference $conference;

    public function __construct(Registration $registration, Conference $conference)
    {
        $this->registration = $registration;
        $this->conference = $conference;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'), 
                $this->conference->company->name  // Use company name here
            ),
            subject: 'New Registration - ' . $this->conference->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}