<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\EventAttendee;
use App\Models\ComplementaryTicket;
use App\Models\EventOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketVerificationController extends Controller
{
    /**
     * Show scanner interface for organizers
     */
    public function showScanner(Event $event)
    {
        $company = auth()->guard('company')->user();
        if ($event->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this event');
        }

        $stats = $this->getEventStats($event);

        return view('organization.ticket-scanner', compact('event', 'stats'));
    }

    /**
     * Show scanner interface for staff (simple one-page scanner)
     */
    public function showStaffScanner()
    {
        $staff = auth()->guard('staff')->user();

        // Get events this staff member can access
        $events = Event::where('company_id', $staff->company_id)
                      ->when($staff->event_ids, function($q) use ($staff) {
                          $q->whereIn('id', $staff->event_ids);
                      })
                      ->get();

        return view('staff.scanner', compact('staff', 'events'));
    }

    /**
     * Verify ticket (works for both company and staff)
     * Supports both purchased tickets (EventAttendee) and complementary tickets (ComplementaryTicket)
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'ticket_code' => 'required|string',
            'event_id' => 'required|exists:events,id',
            'method' => 'required|in:qr,manual',
            'notes' => 'nullable|string|max:500'
        ]);

        // Determine who is checking in
        $checkedInByCompany = null;
        $checkedInByStaff = null;

        if (auth()->guard('company')->check()) {
            $checkedInByCompany = auth()->guard('company')->id();
        } elseif (auth()->guard('staff')->check()) {
            $staff = auth()->guard('staff')->user();
            $checkedInByStaff = $staff->id;
            $checkedInByCompany = $staff->company_id;

            if (!$staff->canAccessEvent($validated['event_id'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to this event'
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required'
            ], 401);
        }

        // Try to find ticket in EventAttendee (purchased tickets) first
        $attendeeTicket = EventAttendee::where('ticket_code', $validated['ticket_code'])
                                      ->where('event_id', $validated['event_id'])
                                      ->first();

        if ($attendeeTicket) {
            return $this->verifyAttendeeTicket($attendeeTicket, $validated, $checkedInByStaff, $checkedInByCompany, $request);
        }

        // Try to find in ComplementaryTicket (free tickets)
        $compTicket = ComplementaryTicket::where('qr_code', $validated['ticket_code'])
                                         ->where('event_id', $validated['event_id'])
                                         ->first();

        if ($compTicket) {
            return $this->verifyComplementaryTicket($compTicket, $validated, $checkedInByStaff, $checkedInByCompany, $request);
        }

        // Ticket not found
        return response()->json([
            'status' => 'invalid',
            'message' => 'Ticket not found or does not belong to this event'
        ], 404);
    }

    /**
     * Verify and check in an attendee ticket (purchased)
     */
    private function verifyAttendeeTicket($ticket, $validated, $checkedInByStaff, $checkedInByCompany, $request)
    {
        // Check if already checked in
        if ($ticket->checked_in) {
            $checkedInBy = 'Unknown';
            if ($checkedInByStaff) {
                $staff = \App\Models\OrganizationStaff::find($checkedInByStaff);
                $checkedInBy = $staff ? $staff->name . ' (Staff)' : 'Staff';
            } elseif ($checkedInByCompany) {
                $company = \App\Models\Company::find($checkedInByCompany);
                $checkedInBy = $company ? $company->name . ' (Organizer)' : 'Organizer';
            }

            return response()->json([
                'status' => 'duplicate',
                'message' => 'Ticket already used',
                'checked_in_at' => $ticket->checked_in_at ? $ticket->checked_in_at->format('M j, Y g:i A') : 'Unknown time',
                'checked_in_by' => $checkedInBy,
                'ticket' => [
                    'holder_name' => $ticket->attendee_name,
                    'holder_email' => $ticket->attendee_email,
                    'ticket_type' => 'Purchased Ticket',
                ]
            ], 400);
        }

        // Check in the ticket
        DB::beginTransaction();
        try {
            $ticket->update([
                'checked_in' => true,
                'checked_in_at' => now(),
                'checked_in_by' => $checkedInByStaff ?? $checkedInByCompany,
            ]);

            DB::commit();

            $staffName = $checkedInByStaff
                ? auth()->guard('staff')->user()->name
                : auth()->guard('company')->user()->name;

            return response()->json([
                'status' => 'valid',
                'message' => 'Ticket verified successfully',
                'ticket' => [
                    'holder_name' => $ticket->attendee_name,
                    'holder_email' => $ticket->attendee_email,
                    'ticket_type' => 'Purchased Ticket',
                    'checked_in_by' => $staffName,
                    'checked_in_at' => $ticket->checked_in_at->format('M j, Y g:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ticket check-in failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check in ticket. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify and check in a complementary ticket (free)
     */
    private function verifyComplementaryTicket($ticket, $validated, $checkedInByStaff, $checkedInByCompany, $request)
    {
        // Check if already used
        if ($ticket->status === 'used') {
            $checkedInBy = 'Unknown';
            if ($ticket->scanned_by) {
                $admin = \App\Models\Admin::find($ticket->scanned_by);
                $checkedInBy = $admin ? $admin->name . ' (Admin)' : 'Admin';
            }

            return response()->json([
                'status' => 'duplicate',
                'message' => 'Complementary ticket already used',
                'checked_in_at' => $ticket->used_at ? $ticket->used_at->format('M j, Y g:i A') : 'Unknown time',
                'checked_in_by' => $checkedInBy,
                'ticket' => [
                    'holder_name' => $ticket->recipient_name,
                    'holder_email' => $ticket->recipient_email,
                    'ticket_type' => 'Complementary Ticket',
                ]
            ], 400);
        }

        // Check if cancelled
        if ($ticket->status === 'cancelled') {
            return response()->json([
                'status' => 'invalid',
                'message' => 'This complementary ticket has been cancelled'
            ], 400);
        }

        // Check in the ticket
        DB::beginTransaction();
        try {
            $ticket->markAsUsed($checkedInByStaff ?? $checkedInByCompany);

            DB::commit();

            $staffName = $checkedInByStaff
                ? auth()->guard('staff')->user()->name
                : auth()->guard('company')->user()->name;

            return response()->json([
                'status' => 'valid',
                'message' => 'Complementary ticket verified successfully',
                'ticket' => [
                    'holder_name' => $ticket->recipient_name,
                    'holder_email' => $ticket->recipient_email,
                    'ticket_type' => $ticket->ticket_type ?? 'Complementary Ticket',
                    'checked_in_by' => $staffName,
                    'checked_in_at' => $ticket->used_at->format('M j, Y g:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Complementary ticket check-in failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check in complementary ticket. Please try again.'
            ], 500);
        }
    }

    /**
     * Get event statistics
     */
    private function getEventStats(Event $event)
    {
        // Count purchased tickets
        $purchasedTotal = EventAttendee::where('event_id', $event->id)->count();
        $purchasedCheckedIn = EventAttendee::where('event_id', $event->id)
                                          ->where('checked_in', true)
                                          ->count();

        // Count complementary tickets
        $compTotal = ComplementaryTicket::where('event_id', $event->id)
                                       ->whereIn('status', ['active', 'used'])
                                       ->count();
        $compUsed = ComplementaryTicket::where('event_id', $event->id)
                                      ->where('status', 'used')
                                      ->count();

        $totalTickets = $purchasedTotal + $compTotal;
        $checkedInTickets = $purchasedCheckedIn + $compUsed;
        $pendingTickets = $totalTickets - $checkedInTickets;
        $checkInRate = $totalTickets > 0 ? round(($checkedInTickets / $totalTickets) * 100, 1) : 0;

        return [
            'total' => $totalTickets,
            'checked_in' => $checkedInTickets,
            'pending' => $pendingTickets,
            'rate' => $checkInRate,
        ];
    }

    /**
     * Get real-time check-in activity
     */
    public function getActivity(Event $event)
    {
        // Get recent purchased ticket check-ins
        $purchasedCheckins = EventAttendee::where('event_id', $event->id)
            ->where('checked_in', true)
            ->whereNotNull('checked_in_at')
            ->orderBy('checked_in_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($ticket) {
                $checkedInBy = 'Unknown';
                if ($ticket->checked_in_by) {
                    $staff = \App\Models\OrganizationStaff::find($ticket->checked_in_by);
                    if ($staff) {
                        $checkedInBy = $staff->name . ' (Staff)';
                    } else {
                        $company = \App\Models\Company::find($ticket->checked_in_by);
                        $checkedInBy = $company ? $company->name . ' (Organizer)' : 'Organizer';
                    }
                }

                return [
                    'holder_name' => $ticket->attendee_name,
                    'ticket_code' => $ticket->ticket_code,
                    'checked_in_at' => $ticket->checked_in_at->diffForHumans(),
                    'checked_in_by' => $checkedInBy,
                    'method' => 'Purchased',
                ];
            });

        // Get recent complementary ticket check-ins
        $compCheckins = ComplementaryTicket::where('event_id', $event->id)
            ->where('status', 'used')
            ->whereNotNull('used_at')
            ->orderBy('used_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($ticket) {
                $checkedInBy = 'Unknown';
                if ($ticket->scanned_by) {
                    $admin = \App\Models\Admin::find($ticket->scanned_by);
                    $checkedInBy = $admin ? $admin->name . ' (Admin)' : 'Admin';
                }

                return [
                    'holder_name' => $ticket->recipient_name,
                    'ticket_code' => $ticket->qr_code,
                    'checked_in_at' => $ticket->used_at->diffForHumans(),
                    'checked_in_by' => $checkedInBy,
                    'method' => 'Complementary',
                ];
            });

        // Merge and sort by time
        $recentCheckins = $purchasedCheckins->merge($compCheckins)
                                           ->sortByDesc('checked_in_at')
                                           ->take(10)
                                           ->values();

        $stats = $this->getEventStats($event);

        return response()->json([
            'stats' => $stats,
            'recent_checkins' => $recentCheckins
        ]);
    }
}
