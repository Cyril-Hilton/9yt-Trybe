<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\Registration;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RegistrationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected EmailService $emailService
    ) {}

    public function index(Conference $conference, Request $request)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        $query = $conference->registrations();

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('unique_id', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by attendance type
        if ($request->filled('attendance_type') && $request->attendance_type !== 'all') {
            $query->where('attendance_type', $request->attendance_type);
        }

        // Filter by attended status
        if ($request->filled('attended')) {
            if ($request->attended === 'yes') {
                $query->where('attended', true);
            } elseif ($request->attended === 'no') {
                $query->where('attended', false);
            }
        }

        $registrations = $query->latest()->paginate(20);
        
        // Load custom fields
        $conference->load('customFields');

        return view('company.registrations.index', compact('conference', 'registrations'));
    }

    public function show(Conference $conference, Registration $registration)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        if ($registration->conference_id !== $conference->id) {
            abort(404);
        }

        // Load the custom fields relationship
        $conference->load('customFields');

        return view('company.registrations.show', compact('conference', 'registration'));
    }

    public function destroy(Conference $conference, Registration $registration)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        if ($registration->conference_id !== $conference->id) {
            abort(404);
        }

        $name = $registration->name;
        $attendanceType = $registration->attendance_type;

        $registration->delete();

        // Decrement the conference attendance count
        $conference->decrementCount($attendanceType);

        return redirect()->route('organization.conferences.registrations.index', $conference)
            ->with('success', "Registration for {$name} has been deleted.");
    }

    public function markAttendance(Request $request, Conference $conference)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        $validated = $request->validate([
            'unique_id' => ['required', 'string', 'size:4'],
        ]);

        $registration = $conference->registrations()
            ->where('unique_id', $validated['unique_id'])
            ->where('attendance_type', 'in_person')
            ->firstOrFail();

        if ($registration->attended) {
            return back()->with('error', 'This attendee has already been marked as attended.');
        }

        $registration->update(['attended' => true]);

        return back()->with('success', "Attendance marked for {$registration->name} (ID: {$registration->unique_id})");
    }

    public function showBulkEmailForm(Conference $conference)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }
        
        return view('company.registrations.bulk-email', compact('conference'));
    }

    public function sendBulkEmail(Request $request, Conference $conference)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403, 'This conference does not belong to your company.');
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'filter' => ['required', 'in:all,online,in_person'],
        ]);

        $query = $conference->registrations();

        if ($validated['filter'] !== 'all') {
            $query->where('attendance_type', $validated['filter']);
        }

        $count = $query->count();

        $this->emailService->sendBulkEmail(
            $conference,
            $validated['subject'],
            $validated['message'],
            $validated['filter'] === 'all' ? null : $validated['filter']
        );

        return back()->with('success', "Bulk email queued for {$count} registrants!");
    }
}