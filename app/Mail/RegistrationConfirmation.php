<?php

namespace App\Mail;

use App\Models\Registration;
use App\Models\Conference;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmation extends Mailable
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
            subject: 'Registration Confirmed - ' . $this->conference->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
            with: [
                'registration' => $this->registration,
                'conference' => $this->conference,
                'companyName' => $this->conference->company->name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}