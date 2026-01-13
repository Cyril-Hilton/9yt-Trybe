<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\Registration;
use App\Mail\BulkEmail;
use App\Mail\RegistrationConfirmation;
use App\Mail\CompanyNotification;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send registration confirmation email to the registrant
     */
    public function sendRegistrationConfirmation(Registration $registration): void
    {
        Mail::to($registration->email)
            ->queue(new RegistrationConfirmation($registration, $registration->conference));
    }

    /**
     * Send notification email to the company
     */
    public function sendCompanyNotification(Registration $registration): void
    {
        Mail::to($registration->conference->company->email)
            ->queue(new CompanyNotification($registration, $registration->conference));
    }

    /**
     * Send bulk email to registrants based on filter
     */
    public function sendBulkEmail(Conference $conference, string $subject, string $message, ?string $filter = 'all'): int
    {
        $query = $conference->registrations();

        // Handle null as 'all'
        if ($filter === null) {
            $filter = 'all';
        }

        if ($filter === 'online') {
            $query->where('attendance_type', 'online');
        } elseif ($filter === 'in_person') {
            $query->where('attendance_type', 'in_person');
        }

        $registrations = $query->get();

        foreach ($registrations as $registration) {
            Mail::to($registration->email)->queue(
                new BulkEmail($registration, $conference, $subject, $message)
            );
        }

        return $registrations->count();
    }

    /**
     * Send bulk email to selected registrants
     */
    public function sendBulkEmailToSelected(Conference $conference, array $registrationIds, string $subject, string $message): int
    {
        $registrations = $conference->registrations()
            ->whereIn('id', $registrationIds)
            ->get();

        foreach ($registrations as $registration) {
            Mail::to($registration->email)->queue(
                new BulkEmail($registration, $conference, $subject, $message)
            );
        }

        return $registrations->count();
    }

    /**
     * Send reminder to non-attended registrants
     */
    public function sendReminderToNonAttended(Conference $conference, string $subject, string $message): int
    {
        $registrations = $conference->inPersonRegistrations()
            ->where('attended', false)
            ->get();

        foreach ($registrations as $registration) {
            Mail::to($registration->email)->queue(
                new BulkEmail($registration, $conference, $subject, $message)
            );
        }

        return $registrations->count();
    }
}