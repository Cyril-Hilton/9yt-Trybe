<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Services\EmailService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class ConferenceController extends Controller
{
    use AuthorizesRequests;  // Add this line

    public function __construct(
        protected EmailService $emailService,
        protected ExportService $exportService
    ) {}

    public function index(Request $request)
    {
        $query = auth()->guard('company')->user()->conferences();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $conferences = $query->withCount([
            'registrations',
            'registrations as online_registrations_count' => fn($q) => $q->where('attendance_type', 'online'),
            'registrations as in_person_registrations_count' => fn($q) => $q->where('attendance_type', 'in_person'),
        ])->latest()->paginate(12);

        return view('company.conferences.index', compact('conferences'));
    }

    public function create()
    {
        return view('company.conferences.create');
    }

    public function store(Request $request)
    {
 
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'header_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'venue' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'after:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'online_limit' => ['required', 'integer', 'min:0'],
            'in_person_limit' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive,closed'],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['company_id'] = auth()->guard('company')->id();
       // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('conferences/logos', 'public');
        }

        // Handle header image upload
        if ($request->hasFile('header_image')) {
            $validated['header_image'] = $request->file('header_image')->store('conferences/headers', 'public');
        }
        $conference = Conference::create($validated);

        return redirect()->route('organization.conferences.show', $conference)
            ->with('success', 'Conference created successfully!');
    }

    public function show(Conference $conference)
    {
        $this->authorize('view', $conference);

        $conference->load(['registrations' => fn($q) => $q->latest()]);

        $stats = [
            'total_registrations' => $conference->registrations()->count(),
            'online_registrations' => $conference->onlineRegistrations()->count(),
            'in_person_registrations' => $conference->inPersonRegistrations()->count(),
            'attended_count' => $conference->attendedRegistrations()->count(),
            'views_count' => $conference->views_count,
            'conversion_rate' => $conference->conversion_rate,
            'attendance_rate' => $conference->attendance_rate,
        ];

        return view('company.conferences.show', compact('conference', 'stats'));
    }

    public function edit(Conference $conference)
    {
        $this->authorize('update', $conference);

        return view('company.conferences.edit', compact('conference'));
    }

    public function update(Request $request, Conference $conference)
    {
        $this->authorize('update', $conference);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image'],
            'header_image' => ['nullable', 'image'],
            'venue' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'online_limit' => ['required', 'integer', 'min:0'],
            'in_person_limit' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive,closed'],
        ]);
// Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($conference->logo) {
                Storage::disk('public')->delete($conference->logo);
            }
            $validated['logo'] = $request->file('logo')->store('conferences/logos', 'public');
        }

        // Handle header image upload
        if ($request->hasFile('header_image')) {
            // Delete old header
            if ($conference->header_image) {
                Storage::disk('public')->delete($conference->header_image);
            }
            $validated['header_image'] = $request->file('header_image')->store('conferences/headers', 'public');
        }
        $conference->update($validated);

        return redirect()->route('organization.conferences.show', $conference)
            ->with('success', 'Conference updated successfully!');
    }
public function removeLogo(Conference $conference)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        if ($conference->logo) {
            Storage::disk('public')->delete($conference->logo);
            $conference->update(['logo' => null]);
        }

        return back()->with('success', 'Logo removed successfully!');
    }

    public function removeHeader(Conference $conference)
    {
        // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        if ($conference->header_image) {
            Storage::disk('public')->delete($conference->header_image);
            $conference->update(['header_image' => null]);
        }

        return back()->with('success', 'Header image removed successfully!');
    }

    public function destroy(Conference $conference)
    {
        $this->authorize('delete', $conference);
  // Direct ownership check
        if ($conference->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        // Delete images
        if ($conference->logo) {
            Storage::disk('public')->delete($conference->logo);
        }
        if ($conference->header_image) {
            Storage::disk('public')->delete($conference->header_image);
        }
        $conference->delete();

        return redirect()->route('organization.conferences.index')
            ->with('success', 'Conference deleted successfully!');
    }

    public function bulkEmail(Conference $conference)
    {
        $this->authorize('view', $conference);

        return view('company.registrations.bulk-email', compact('conference'));
    }

    public function sendBulkEmail(Request $request, Conference $conference)
    {
        $this->authorize('view', $conference);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'attendance_type' => ['nullable', 'in:online,in_person'],
        ]);

        $this->emailService->sendBulkEmail(
            $conference,
            $validated['subject'],
            $validated['message'],
            $validated['attendance_type'] ?? null
        );

        return back()->with('success', 'Emails queued successfully!');
    }

    public function export(Conference $conference, string $format)
    {
        $this->authorize('view', $conference);

        return $this->exportService->exportRegistrations($conference, $format);
    }
}
