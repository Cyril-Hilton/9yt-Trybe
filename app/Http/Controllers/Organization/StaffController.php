<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\OrganizationStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class StaffController extends Controller
{
    public function index()
    {
        $company = auth()->guard('company')->user();
        $staff = OrganizationStaff::where('company_id', $company->id)
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(20);

        return view('organization.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('organization.staff.create');
    }

    public function store(Request $request)
    {
        $company = auth()->guard('company')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organization_staff,email',
            'phone' => 'required|string|unique:organization_staff,phone',
            'event_ids' => 'nullable|array',
            'event_ids.*' => 'exists:events,id',
        ]);

        // Verify that all selected events belong to this company
        if (!empty($validated['event_ids'])) {
            $validEventIds = \App\Models\Event::where('company_id', $company->id)
                                              ->whereIn('id', $validated['event_ids'])
                                              ->pluck('id')
                                              ->toArray();

            if (count($validEventIds) !== count($validated['event_ids'])) {
                return back()->withErrors(['event_ids' => 'You can only assign your own events to attendants.'])
                            ->withInput();
            }

            $validated['event_ids'] = $validEventIds;
        }

        $staff = OrganizationStaff::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'event_ids' => $validated['event_ids'] ?? null,
            'status' => 'active',
        ]);

        // Send email with login link
        try {
            // For now, send a simple notification (you can create a proper mail class later)
            \Mail::raw(
                "Hello {$staff->name},\n\n" .
                "You have been added as a ticket scanning attendant.\n\n" .
                "Login here: " . route('staff.login') . "\n" .
                "Your phone number: {$staff->phone}\n\n" .
                "You will receive an OTP code when you login.\n\n" .
                "Best regards,\n" .
                "{$company->name}",
                function ($message) use ($staff, $company) {
                    $message->to($staff->email)
                           ->subject("Ticket Scanner Access - {$company->name}");
                }
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send staff welcome email: ' . $e->getMessage());
        }

        return redirect()->route('organization.staff.index')
                        ->with('success', "Attendant added successfully! Login link sent to {$staff->email}");
    }

    public function show(OrganizationStaff $staff)
    {
        if ($staff->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        // Get events this staff has access to
        $events = [];
        if ($staff->event_ids && count($staff->event_ids) > 0) {
            $events = \App\Models\Event::whereIn('id', $staff->event_ids)->get();
        }

        // Get recent check-ins by this staff
        $recentCheckIns = $staff->checkedInTickets()
                               ->with('order')
                               ->orderBy('checked_in_at', 'desc')
                               ->limit(10)
                               ->get();

        return view('organization.staff.show', compact('staff', 'events', 'recentCheckIns'));
    }

    public function edit(OrganizationStaff $staff)
    {
        if ($staff->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        return view('organization.staff.edit', compact('staff'));
    }

    public function update(Request $request, OrganizationStaff $staff)
    {
        if ($staff->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        $company = auth()->guard('company')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organization_staff,email,' . $staff->id,
            'phone' => 'required|string|unique:organization_staff,phone,' . $staff->id,
            'event_ids' => 'nullable|array',
            'event_ids.*' => 'exists:events,id',
        ]);

        // Verify that all selected events belong to this company
        if (!empty($validated['event_ids'])) {
            $validEventIds = \App\Models\Event::where('company_id', $company->id)
                                              ->whereIn('id', $validated['event_ids'])
                                              ->pluck('id')
                                              ->toArray();

            if (count($validEventIds) !== count($validated['event_ids'])) {
                return back()->withErrors(['event_ids' => 'You can only assign your own events to attendants.'])
                            ->withInput();
            }

            $validated['event_ids'] = $validEventIds;
        }

        $staff->update($validated);

        return redirect()->route('organization.staff.index')
                        ->with('success', 'Attendant updated successfully!');
    }

    public function suspend(OrganizationStaff $staff)
    {
        if ($staff->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        $staff->update(['status' => 'suspended']);

        return redirect()->route('organization.staff.index')
                        ->with('success', 'Attendant suspended successfully!');
    }

    public function activate(OrganizationStaff $staff)
    {
        if ($staff->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        $staff->update(['status' => 'active']);

        return redirect()->route('organization.staff.index')
                        ->with('success', 'Attendant activated successfully!');
    }

    public function destroy(OrganizationStaff $staff)
    {
        if ($staff->company_id !== auth()->guard('company')->id()) {
            abort(403);
        }

        $staff->delete();

        return redirect()->route('organization.staff.index')
                        ->with('success', 'Attendant removed successfully!');
    }
}
