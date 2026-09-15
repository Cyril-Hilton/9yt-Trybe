<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSection;
use App\Models\EventTicket;
use App\Models\EventImage;
use App\Models\EventVideo;
use App\Models\EventFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index()
    {
        $company = Auth::guard('company')->user();
        $events = $company->events()->latest()->paginate(15);

        return view('company.events.index', compact('events'));
    }

    public function create()
    {
        $this->authorize('create', Event::class);

        return view('company.events.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $company = Auth::guard('company')->user();

        $validated = $this->validateEventPayload($request);
        $requiresTicket = $request->input('action') === 'publish';
        $tickets = $this->validatedTicketPayload($request, $requiresTicket);

        DB::transaction(function () use ($validated, $company, $request, $tickets) {
            // Handle banner upload
            if ($request->hasFile('banner_image')) {
                $validated['banner_image'] = $this->storePublicUpload(
                    $request->file('banner_image'),
                    'events/banners',
                    'banner_image'
                );
            }

            // Determine status based on action - organizers submit for admin approval
            $status = $request->input('action') === 'publish' ? 'pending' : 'draft';

            // Create event
            $event = $company->events()->create(array_merge($validated, [
                'status' => $status,
                'region' => $validated['region'] ?? 'Greater Accra',
            ]));

            // Sync categories
            if ($request->has('categories')) {
                $event->categories()->sync($request->input('categories'));
            }

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $imagePath = $this->storePublicUpload($imageFile, 'events/images', 'images.' . $index);
                    EventImage::create([
                        'event_id' => $event->id,
                        'image_path' => $imagePath,
                        'order' => $index,
                    ]);
                }
            }

            // Handle videos
            if ($request->has('videos')) {
                foreach ($request->input('videos', []) as $index => $videoUrl) {
                    $videoUrl = trim((string) $videoUrl);
                    if ($videoUrl === '') {
                        continue;
                    }

                    $platform = EventVideo::detectPlatform($videoUrl);
                    if ($platform) {
                        $videoId = EventVideo::extractVideoId($videoUrl, $platform);
                        EventVideo::create([
                            'event_id' => $event->id,
                            'platform' => $platform,
                            'video_url' => $videoUrl,
                            'video_id' => $videoId,
                            'order' => $index,
                        ]);
                    }
                }
            }

            // Handle FAQs
            if ($request->has('faqs')) {
                foreach ($request->input('faqs', []) as $index => $faq) {
                    $question = trim((string) ($faq['question'] ?? ''));
                    $answer = trim((string) ($faq['answer'] ?? ''));

                    if ($question !== '' && $answer !== '') {
                        EventFaq::create([
                            'event_id' => $event->id,
                            'question' => $question,
                            'answer' => $answer,
                            'order' => $index,
                        ]);
                    }
                }
            }

            // Handle sections
            if ($request->has('sections')) {
                foreach ($request->input('sections', []) as $index => $section) {
                    if (empty($section['name']) || empty($section['capacity'])) {
                        continue;
                    }

                    EventSection::create([
                        'event_id' => $event->id,
                        'name' => $section['name'],
                        'capacity' => $section['capacity'],
                        'order' => $index,
                    ]);
                }
            }

            // Handle tickets
            if ($tickets !== []) {
                foreach ($tickets as $index => $ticket) {
                    EventTicket::create([
                        'event_id' => $event->id,
                        'event_section_id' => $ticket['section_id'] ?? null,
                        'name' => $ticket['name'],
                        'description' => $ticket['description'] ?? null,
                        'type' => $ticket['type'],
                        'price' => $ticket['type'] === 'free' ? 0 : ($ticket['price'] ?? 0),
                        'min_donation' => $ticket['type'] === 'donation' ? ($ticket['min_donation'] ?? 0) : null,
                        'quantity' => $ticket['quantity'] ?? null,
                        'min_per_order' => $ticket['min_per_order'] ?? 1,
                        'max_per_order' => $ticket['max_per_order'] ?? 10,
                        'sales_start' => $ticket['sales_start'] ?? null,
                        'sales_end' => $ticket['sales_end'] ?? null,
                        'is_active' => true,
                        'order' => $index,
                    ]);
                }
            }

            session()->flash('success', 'Event created successfully!');
            session()->flash('event_id', $event->id);
        });

        $eventId = session('event_id');
        return redirect()->route('organization.events.show', $eventId);
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event->load(['tickets', 'sections', 'images', 'videos', 'faqs', 'orders', 'attendees']);

        $stats = [
            'total_tickets_sold' => $event->tickets_sold,
            'total_revenue' => $event->total_revenue,
            'total_views' => $event->views_count,
            'total_likes' => $event->likes_count,
            'pending_orders' => $event->orders()->where('payment_status', 'pending')->count(),
            'completed_orders' => $event->orders()->where('payment_status', 'completed')->count(),
        ];

        return view('company.events.show', compact('event', 'stats'));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $event->load(['tickets', 'sections', 'images', 'videos', 'faqs']);

        return view('company.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $this->validateEventPayload($request);
        $tickets = $this->validatedTicketPayload($request, false);

        DB::transaction(function () use ($validated, $event, $request, $tickets) {
            // Handle banner upload
            if ($request->hasFile('banner_image')) {
                // Delete old banner
                if ($event->banner_image) {
                    Storage::disk('public')->delete($event->banner_image);
                }
                $validated['banner_image'] = $this->storePublicUpload(
                    $request->file('banner_image'),
                    'events/banners',
                    'banner_image'
                );
            }

            // Update event
            $event->update($validated);

            // Sync categories
            if ($request->has('categories')) {
                $event->categories()->sync($request->input('categories'));
            } else {
                // If no categories provided, detach all
                $event->categories()->detach();
            }

            // Update images - handle new uploads
            if ($request->hasFile('images')) {
                // Delete old images
                foreach ($event->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $event->images()->delete();

                // Upload new images
                foreach ($request->file('images') as $index => $imageFile) {
                    $imagePath = $this->storePublicUpload($imageFile, 'events/images', 'images.' . $index);
                    EventImage::create([
                        'event_id' => $event->id,
                        'image_path' => $imagePath,
                        'order' => $index,
                    ]);
                }
            }

            // Update videos - delete existing and recreate
            $event->videos()->delete();
            if ($request->has('videos')) {
                foreach ($request->input('videos', []) as $index => $videoUrl) {
                    $videoUrl = trim((string) $videoUrl);
                    if ($videoUrl === '') {
                        continue;
                    }

                    $platform = EventVideo::detectPlatform($videoUrl);
                    if ($platform) {
                        $videoId = EventVideo::extractVideoId($videoUrl, $platform);
                        EventVideo::create([
                            'event_id' => $event->id,
                            'platform' => $platform,
                            'video_url' => $videoUrl,
                            'video_id' => $videoId,
                            'order' => $index,
                        ]);
                    }
                }
            }

            // Update FAQs - delete existing and recreate
            $event->faqs()->delete();
            if ($request->has('faqs')) {
                foreach ($request->input('faqs', []) as $index => $faq) {
                    $question = trim((string) ($faq['question'] ?? ''));
                    $answer = trim((string) ($faq['answer'] ?? ''));

                    if ($question !== '' && $answer !== '') {
                        EventFaq::create([
                            'event_id' => $event->id,
                            'question' => $question,
                            'answer' => $answer,
                            'order' => $index,
                        ]);
                    }
                }
            }

            // Update sections - delete existing and recreate
            $event->sections()->delete();
            if ($request->has('sections')) {
                foreach ($request->input('sections', []) as $index => $section) {
                    if (empty($section['name']) || empty($section['capacity'])) {
                        continue;
                    }

                    EventSection::create([
                        'event_id' => $event->id,
                        'name' => $section['name'],
                        'capacity' => $section['capacity'],
                        'order' => $index,
                    ]);
                }
            }

            // Update tickets - delete existing and recreate
            // Note: Only do this if no tickets have been sold
            if ($event->tickets_sold == 0) {
                $event->tickets()->delete();
                if ($tickets !== []) {
                    foreach ($tickets as $index => $ticket) {
                        EventTicket::create([
                            'event_id' => $event->id,
                            'event_section_id' => $ticket['section_id'] ?? null,
                            'name' => $ticket['name'],
                            'description' => $ticket['description'] ?? null,
                            'type' => $ticket['type'],
                            'price' => $ticket['type'] === 'free' ? 0 : ($ticket['price'] ?? 0),
                            'min_donation' => $ticket['type'] === 'donation' ? ($ticket['min_donation'] ?? 0) : null,
                            'quantity' => $ticket['quantity'] ?? null,
                            'min_per_order' => $ticket['min_per_order'] ?? 1,
                            'max_per_order' => $ticket['max_per_order'] ?? 10,
                            'sales_start' => $ticket['sales_start'] ?? null,
                            'sales_end' => $ticket['sales_end'] ?? null,
                            'is_active' => true,
                            'order' => $index,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('organization.events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    private function validateEventPayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'overview' => 'nullable|string',
            'event_type' => 'required|in:single,recurring',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'region' => 'required|string',
            'location_type' => 'required|in:venue,online,tba',
            'venue_name' => 'required_if:location_type,venue|nullable|string',
            'venue_address' => 'required_if:location_type,venue|nullable|string',
            'venue_latitude' => 'nullable|numeric',
            'venue_longitude' => 'nullable|numeric',
            'online_platform' => 'required_if:location_type,online|nullable|string',
            'online_link' => 'nullable|url',
            'online_meeting_details' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            'videos' => 'nullable|array',
            'videos.*' => 'nullable|url',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:255',
            'faqs.*.answer' => 'nullable|string',
            'age_restriction' => 'nullable|string',
            'door_time' => 'nullable',
            'parking_info' => 'nullable|string',
            'transportation_enabled' => 'nullable|boolean',
            'transport_seat_capacity' => 'nullable|integer|min:1|max:100',
            'transport_pickup_details' => 'nullable|string|max:2000',
            'fee_bearer' => 'required|in:organizer,attendee',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_holiday' => 'nullable|boolean',
            'holiday_name' => 'nullable|string|max:255',
            'holiday_country' => 'nullable|string|max:255',
        ], [
            'banner_image.image' => 'The event flier must be an image file.',
            'banner_image.mimes' => 'The event flier must be a JPG, PNG, WEBP, or GIF image.',
            'banner_image.max' => 'The event flier is too large. Please upload an image that is 20 MB or smaller.',
            'images.*.image' => 'Every event gallery file must be an image.',
            'images.*.mimes' => 'Event gallery images must be JPG, PNG, WEBP, or GIF files.',
            'images.*.max' => 'Each event gallery image must be 20 MB or smaller.',
        ]);

        return $validated;
    }

    private function validatedTicketPayload(Request $request, bool $requiresTicket): array
    {
        $rawTickets = $request->input('tickets', []);

        if (!is_array($rawTickets)) {
            $rawTickets = [];
        }

        Validator::make(['tickets' => $rawTickets], [
            'tickets' => 'nullable|array',
            'tickets.*.name' => 'nullable|string|max:255',
            'tickets.*.description' => 'nullable|string',
            'tickets.*.type' => 'nullable|in:paid,free,donation',
            'tickets.*.price' => 'nullable|numeric|min:0',
            'tickets.*.min_donation' => 'nullable|numeric|min:0',
            'tickets.*.quantity' => 'nullable|integer|min:1',
            'tickets.*.min_per_order' => 'nullable|integer|min:1',
            'tickets.*.max_per_order' => 'nullable|integer|min:1',
            'tickets.*.sales_start' => 'nullable|date',
            'tickets.*.sales_end' => 'nullable|date',
        ])->validate();

        $tickets = [];
        $errors = [];

        foreach ($rawTickets as $index => $ticket) {
            if (!is_array($ticket)) {
                continue;
            }

            $name = trim((string) ($ticket['name'] ?? ''));
            $description = trim((string) ($ticket['description'] ?? ''));
            $type = (string) ($ticket['type'] ?? 'paid');
            $price = $ticket['price'] ?? null;
            $minDonation = $ticket['min_donation'] ?? null;
            $quantity = $ticket['quantity'] ?? null;
            $salesStart = $ticket['sales_start'] ?? null;
            $salesEnd = $ticket['sales_end'] ?? null;

            $hasAnyTicketInput = $name !== ''
                || $description !== ''
                || $this->hasInputValue($price)
                || $this->hasInputValue($minDonation)
                || $this->hasInputValue($quantity)
                || $this->hasInputValue($salesStart)
                || $this->hasInputValue($salesEnd);

            if (!$hasAnyTicketInput) {
                continue;
            }

            if ($name === '') {
                $errors["tickets.$index.name"] = 'Add a ticket name or remove the blank ticket row.';
            }

            if ($type === 'paid' && !$this->hasInputValue($price)) {
                $errors["tickets.$index.price"] = 'Add a price for paid tickets, choose Free, or remove the blank ticket row.';
            }

            $minPerOrder = max(1, (int) ($ticket['min_per_order'] ?? 1));
            $maxPerOrder = max(1, (int) ($ticket['max_per_order'] ?? 10));

            if ($maxPerOrder < $minPerOrder) {
                $errors["tickets.$index.max_per_order"] = 'Max tickets per order must be greater than or equal to the minimum.';
            }

            $tickets[] = [
                'section_id' => $ticket['section_id'] ?? null,
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'type' => $type,
                'price' => $type === 'free' ? 0 : (float) ($price ?? 0),
                'min_donation' => $type === 'donation' ? (float) ($minDonation ?? 0) : null,
                'quantity' => $this->hasInputValue($quantity) ? (int) $quantity : null,
                'min_per_order' => $minPerOrder,
                'max_per_order' => $maxPerOrder,
                'sales_start' => $this->hasInputValue($salesStart) ? $salesStart : null,
                'sales_end' => $this->hasInputValue($salesEnd) ? $salesEnd : null,
            ];
        }

        if ($requiresTicket && $tickets === []) {
            $errors['tickets'] = 'Add at least one ticket before publishing, or save the event as a draft.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $tickets;
    }

    private function hasInputValue(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    private function storePublicUpload($file, string $directory, string $field): string
    {
        $path = $file->store($directory, 'public');

        if (!$path) {
            throw ValidationException::withMessages([
                $field => 'The image could not be saved. Please try again or upload a smaller image.',
            ]);
        }

        return $path;
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        // Check if event has orders
        if ($event->orders()->where('payment_status', 'completed')->exists()) {
            return back()->with('error', 'Cannot delete event with completed orders.');
        }

        DB::transaction(function () use ($event) {
            // Delete banner image
            if ($event->banner_image) {
                Storage::disk('public')->delete($event->banner_image);
            }

            // Delete event images
            foreach ($event->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $event->delete();
        });

        return redirect()->route('organization.events.index')
            ->with('success', 'Event deleted successfully!');
    }

    public function publish(Event $event)
    {
        $this->authorize('update', $event);

        // Validate event has required fields
        if (!$event->hasActiveTickets()) {
            return back()->with('error', 'Please add at least one active ticket before publishing.');
        }

        $event->update([
            'status' => 'pending',
            'region' => $event->region ?: 'Greater Accra',
        ]);

        return back()->with('success', 'Event submitted for approval!');
    }

    public function duplicate(Event $event)
    {
        $this->authorize('view', $event);

        $company = Auth::guard('company')->user();

        DB::transaction(function () use ($event, $company) {
            $newEvent = $event->replicate();
            $newEvent->company_id = $company->id;
            $newEvent->title = $event->title . ' (Copy)';
            $newEvent->slug = null; // Will be auto-generated
            $newEvent->status = 'draft';
            $newEvent->views_count = 0;
            $newEvent->likes_count = 0;
            $newEvent->tickets_sold = 0;
            $newEvent->total_revenue = 0;
            $newEvent->save();

            // Duplicate tickets
            foreach ($event->tickets as $ticket) {
                $newTicket = $ticket->replicate();
                $newTicket->event_id = $newEvent->id;
                $newTicket->sold = 0;
                $newTicket->save();
            }

            // Duplicate sections
            foreach ($event->sections as $section) {
                $newSection = $section->replicate();
                $newSection->event_id = $newEvent->id;
                $newSection->sold = 0;
                $newSection->save();
            }

            // Duplicate FAQs
            foreach ($event->faqs as $faq) {
                $newFaq = $faq->replicate();
                $newFaq->event_id = $newEvent->id;
                $newFaq->save();
            }

            session()->flash('success', 'Event duplicated successfully!');
            session()->flash('new_event_id', $newEvent->id);
        });

        $newEventId = session('new_event_id');
        return redirect()->route('organization.events.edit', $newEventId);
    }
}
