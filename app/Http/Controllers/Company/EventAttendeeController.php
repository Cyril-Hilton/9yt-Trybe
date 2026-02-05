<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Http\Request;

class EventAttendeeController extends Controller
{
    public function index(Event $event)
    {
        $this->authorize('view', $event);

        $attendees = $event->attendees()
            ->with(['ticket', 'order'])
            ->latest()
            ->paginate(50);

        $stats = [
            'total_attendees' => $event->attendees()->count(),
            'checked_in' => $event->attendees()->where('checked_in', true)->count(),
            'not_checked_in' => $event->attendees()->where('checked_in', false)->count(),
        ];

        return view('company.events.attendees.index', compact('event', 'attendees', 'stats'));
    }

    public function checkIn(Request $request, Event $event, EventAttendee $attendee)
    {
        $this->authorize('view', $event);

        if ($attendee->event_id != $event->id) {
            abort(403);
        }

        if ($attendee->checked_in) {
            return back()->with('error', 'Attendee already checked in.');
        }

        $attendee->checkIn(auth('company')->id());

        return back()->with('success', "{$attendee->attendee_name} checked in successfully!");
    }

    public function export(Event $event)
    {
        $this->authorize('view', $event);

        $attendees = $event->attendees()->with(['ticket', 'order'])->get();

        $csvData = [];
        $csvData[] = ['Ticket Code', 'Name', 'Email', 'Ticket Type', 'Price Paid', 'Order Number', 'Checked In', 'Purchase Date'];

        foreach ($attendees as $attendee) {
            $csvData[] = [
                $attendee->ticket_code,
                $attendee->attendee_name,
                $attendee->attendee_email,
                $attendee->ticket->name,
                'GH₵ ' . number_format($attendee->price_paid, 2),
                $attendee->order->order_number,
                $attendee->checked_in ? 'Yes' : 'No',
                $attendee->created_at->format('Y-m-d H:i'),
            ];
        }

        $filename = 'attendees-' . $event->slug . '-' . now()->format('Y-m-d') . '.csv';

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
