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

class BulkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Registration $registration;
    public Conference $conference;
    public string $emailSubject;
    public string $emailMessage;

    public function __construct(
        Registration $registration, 
        Conference $conference, 
        string $subject, 
        string $message
    ) {
        $this->registration = $registration;
        $this->conference = $conference;
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
        
        // Eager load company if not already loaded
        if (!$this->conference->relationLoaded('company')) {
            $this->conference->load('company');
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'), 
                $this->conference->company->name ?? config('app.name')  // Fallback to app name
            ),
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bulk-email',
            with: [
                'registration' => $this->registration,
                'conference' => $this->conference,
                'subject' => $this->emailSubject,
                'message' => $this->emailMessage,
                'companyName' => $this->conference->company->name ?? config('app.name'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}