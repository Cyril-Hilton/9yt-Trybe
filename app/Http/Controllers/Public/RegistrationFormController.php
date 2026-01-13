<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\Registration;
use App\Services\EmailService;
use App\Services\UniqueIdGenerator;
use Illuminate\Http\Request;

class RegistrationFormController extends Controller
{
    public function __construct(
        protected EmailService $emailService,
        protected UniqueIdGenerator $uniqueIdGenerator
    ) {}

    public function show(string $slug)
    {
        $conference = Conference::where('slug', $slug)
            ->where('status', 'active')
            ->with('customFields')  // Add this
            ->firstOrFail();

        // Track view
        $conference->views()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('public.form', compact('conference'));
    }

    public function submit(Request $request, string $slug)
    {
        $conference = Conference::where('slug', $slug)
            ->where('status', 'active')
            ->with('customFields')  // Add this
            ->firstOrFail();

        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'attendance_type' => ['required', 'in:online,in_person'],
        ];

        // Add validation for custom fields
        foreach ($conference->customFields as $field) {
            $fieldRules = [];
            
            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific validation
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'select':
                case 'radio':
                    if ($field->options) {
                        $fieldRules[] = 'in:' . implode(',', $field->getOptionsArray());
                    }
                    break;
                case 'checkbox':
                    $fieldRules[] = 'array';
                    break;
            }

            $rules['custom_' . $field->field_name] = $fieldRules;
        }

        $validated = $request->validate($rules);

        // Use database transaction to prevent race conditions
        return \DB::transaction(function () use ($conference, $validated, $request) {
            // Refresh conference data to get latest counts
            $conference->refresh();

            // Check availability
            if ($validated['attendance_type'] === 'online' && !$conference->isOnlineAvailable()) {
                return back()->with('error', 'Online attendance is full. All available spots have been taken.')->withInput();
            }

            if ($validated['attendance_type'] === 'in_person' && !$conference->isInPersonAvailable()) {
                return back()->with('error', 'In-person attendance is full. All available spots have been taken.')->withInput();
            }

            // Check for duplicate registration
            $existingRegistration = $conference->registrations()
                ->where('email', $validated['email'])
                ->first();

            if ($existingRegistration) {
                return back()->with('error', 'You have already registered for this conference.')->withInput();
            }

            // Extract custom field data
            $customData = [];
            foreach ($conference->customFields as $field) {
                $fieldKey = 'custom_' . $field->field_name;
                if (isset($validated[$fieldKey])) {
                    $customData[$field->field_name] = $validated[$fieldKey];
                }
            }

            // Create registration
            $registration = $conference->registrations()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'attendance_type' => $validated['attendance_type'],
                'unique_id' => $validated['attendance_type'] === 'in_person'
                    ? $this->uniqueIdGenerator->generate()
                    : null,
                'custom_data' => $customData,  // Add this
            ]);

            // Increment the conference attendance count
            $conference->incrementCount($validated['attendance_type']);

            // After creating registration, load relationships before sending emails
            $registration->load('conference.company');

            // Send emails
            $this->emailService->sendRegistrationConfirmation($registration);
            $this->emailService->sendCompanyNotification($registration);

            return redirect()->route('thank-you')
                ->with('attendanceType', $registration->attendance_type);
        });
    }

    public function thankYou()
    {
        $attendanceType = session('attendanceType', 'online');
        return view('public.thank-you', compact('attendanceType'));
    }
}