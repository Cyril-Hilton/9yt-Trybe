<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('company')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $events = $query->paginate(20);

        $stats = [
            'total' => Event::count(),
            'pending' => Event::where('status', 'pending')->count(),
            'approved' => Event::where('status', 'approved')->count(),
            'rejected' => Event::where('status', 'rejected')->count(),
        ];

        return view('admin.events.index', compact('events', 'stats'));
    }

    public function create()
    {
        // Get all companies for the dropdown (in case admin wants to assign to an organizer)
        $companies = Company::orderBy('name')
            ->get();

        // Ghana regions
        $regions = [
            'Greater Accra',
            'Ashanti',
            'Western',
            'Eastern',
            'Central',
            'Northern',
            'Upper East',
            'Upper West',
            'Volta',
            'Brong Ahafo',
            'Western North',
            'Ahafo',
            'Bono East',
            'Oti',
            'Savannah',
            'North East',
        ];

        return view('admin.events.create', compact('companies', 'regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'title' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
            'overview' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'timezone' => 'nullable|string',
            'location_type' => 'required|in:venue,online,hybrid',
            'region' => 'nullable|string',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string|max:500',
            'online_platform' => 'nullable|string',
            'online_link' => 'nullable|url',
            'banner_image' => 'nullable|image',
            'is_external' => 'boolean',
            'external_ticket_url' => 'nullable|url|max:500',
            'external_ussd_code' => 'nullable|string|max:50',
            'external_reservation_phone' => 'nullable|string|max:20',
            'external_description' => 'nullable|string',
        ]);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(8);

        // Set status to approved since admin is creating it
        $validated['status'] = 'approved';
        $validated['approved_at'] = now();
        $validated['approved_by'] = Auth::guard('admin')->id();

        // Create event
        $event = Event::create($validated);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'External event created successfully!');
    }

    public function show(Event $event)
    {
        $event->load(['company', 'tickets', 'sections', 'images', 'videos', 'faqs']);

        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        // Get all companies for the dropdown
        $companies = Company::orderBy('name')->get();

        // Ghana regions
        $regions = [
            'Greater Accra',
            'Ashanti',
            'Western',
            'Eastern',
            'Central',
            'Northern',
            'Upper East',
            'Upper West',
            'Volta',
            'Brong Ahafo',
            'Western North',
            'Ahafo',
            'Bono East',
            'Oti',
            'Savannah',
            'North East',
        ];

        return view('admin.events.edit', compact('event', 'companies', 'regions'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'title' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
            'overview' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'timezone' => 'nullable|string',
            'location_type' => 'required|in:venue,online,hybrid',
            'region' => 'nullable|string',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string|max:500',
            'online_platform' => 'nullable|string',
            'online_link' => 'nullable|url',
            'banner_image' => 'nullable|image',
            'is_external' => 'boolean',
            'external_ticket_url' => 'nullable|url|max:500',
            'external_ussd_code' => 'nullable|string|max:50',
            'external_reservation_phone' => 'nullable|string|max:20',
            'external_description' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            try {
                // Delete old banner if exists
                if ($event->banner_image) {
                    \Storage::disk('public')->delete($event->banner_image);
                }
                $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
            } catch (\Exception $e) {
                // Fallback: Use extension-based upload if fileinfo is not available
                $file = $request->file('banner_image');
                $extension = $file->getClientOriginalExtension();
                $filename = 'events/banners/' . \Str::random(40) . '.' . $extension;
                $file->storeAs('', $filename, 'public');
                $validated['banner_image'] = $filename;
            }
        }

        // Update slug if title changed
        if ($validated['title'] !== $event->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(8);
        }

        // Update event
        $event->update($validated);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function approve(Event $event)
    {
        $admin = Auth::guard('admin')->user();

        $event->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'rejection_reason' => null,
        ]);

        // Send notification email to company
        $this->sendApprovalNotification($event, 'approved');

        return back()->with('success', 'Event approved successfully!');
    }

    public function reject(Request $request, Event $event)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $event->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_at' => null,
            'approved_by' => null,
        ]);

        // Send notification email to company
        $this->sendApprovalNotification($event, 'rejected');

        return back()->with('success', 'Event rejected.');
    }

    public function destroy(Event $event)
    {
        $eventTitle = $event->title;

        // Check if event has completed orders
        if ($event->orders()->where('payment_status', 'completed')->exists()) {
            return back()->with('error', 'Cannot delete event with completed orders.');
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$eventTitle}' deleted successfully.");
    }

    protected function sendApprovalNotification(Event $event, string $status)
    {
        try {
            $company = $event->company;

            $subject = $status === 'approved'
                ? "Your event '{$event->title}' has been approved!"
                : "Your event '{$event->title}' requires changes";

            $message = $status === 'approved'
                ? "Congratulations! Your event '{$event->title}' has been approved and is now live."
                : "Your event '{$event->title}' has been rejected. Reason: {$event->rejection_reason}\n\nPlease make the necessary changes and resubmit.";

            Mail::raw($message, function ($mail) use ($company, $subject) {
                $mail->to($company->email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error('Event approval notification failed: ' . $e->getMessage());
        }
    }
}
