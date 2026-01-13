<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventOrder;
use App\Models\EventAttendee;
use App\Models\EventTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketConfirmationMail;

class EventTicketService
{
    protected TicketGeneratorService $ticketGenerator;
    protected QrCodeService $qrCodeService;
    protected FeeCalculatorService $feeCalculator;

    public function __construct(
        TicketGeneratorService $ticketGenerator,
        QrCodeService $qrCodeService,
        FeeCalculatorService $feeCalculator
    ) {
        $this->ticketGenerator = $ticketGenerator;
        $this->qrCodeService = $qrCodeService;
        $this->feeCalculator = $feeCalculator;
    }

    /**
     * Create order with attendees and generate tickets
     *
     * @param Event $event
     * @param array $orderData
     * @param array $tickets Array of ['ticket_id' => quantity]
     * @param array $attendees Array of attendee data with name, email, ticket_id
     * @return EventOrder
     */
    public function createOrder(Event $event, array $orderData, array $tickets, array $attendees = []): EventOrder
    {
        return DB::transaction(function () use ($event, $orderData, $tickets, $attendees) {
            // Lock tickets to prevent race conditions (CRITICAL FIX #6)
            // This prevents two simultaneous purchases from overselling
            $ticketIds = !empty($attendees)
                ? array_column($attendees, 'ticket_id')
                : array_keys($tickets);

            $lockedTickets = EventTicket::whereIn('id', array_unique($ticketIds))
                ->lockForUpdate() // Pessimistic lock
                ->get()
                ->keyBy('id');

            // Re-validate availability with locked tickets
            foreach ($ticketIds as $ticketId) {
                $ticket = $lockedTickets->get($ticketId);
                if (!$ticket) {
                    throw new \Exception("Ticket not found");
                }

                if ($ticket->quantity !== null) {
                    $remaining = $ticket->quantity - $ticket->sold;
                    $requestedQty = !empty($attendees)
                        ? count(array_filter($attendees, fn($a) => $a['ticket_id'] == $ticketId))
                        : ($tickets[$ticketId] ?? 0);

                    if ($requestedQty > $remaining) {
                        throw new \Exception("{$ticket->name}: Only {$remaining} tickets remaining");
                    }
                }
            }

            // Create the order
            $order = EventOrder::create([
                'event_id' => $event->id,
                'user_id' => $orderData['user_id'] ?? null,
                'customer_name' => $orderData['customer_name'],
                'customer_email' => $orderData['customer_email'],
                'customer_phone' => $orderData['customer_phone'] ?? null,
                'subtotal' => $orderData['subtotal'],
                'service_fee' => $orderData['service_fee'],
                'processing_fee' => $orderData['processing_fee'],
                'platform_fee' => $orderData['platform_fee'],
                'total' => $orderData['total'],
                'fee_bearer' => $orderData['fee_bearer'],
                'payment_status' => 'pending',
            ]);

            // Create attendees with individual information
            if (!empty($attendees)) {
                foreach ($attendees as $attendeeData) {
                    $ticket = $lockedTickets->get($attendeeData['ticket_id']);
                    $this->createAttendee($order, $ticket, $attendeeData['name'], $attendeeData['email']);
                }
            } else {
                // Fallback to old behavior if no attendees provided (for backward compatibility)
                foreach ($tickets as $ticketId => $quantity) {
                    $ticket = $lockedTickets->get($ticketId);

                    for ($i = 0; $i < $quantity; $i++) {
                        $this->createAttendee($order, $ticket);
                    }
                }
            }

            return $order;
        });
    }

    /**
     * Create individual attendee with ticket code and QR code
     *
     * @param EventOrder $order
     * @param EventTicket $ticket
     * @param string|null $attendeeName
     * @param string|null $attendeeEmail
     * @return EventAttendee
     */
    protected function createAttendee(EventOrder $order, EventTicket $ticket, ?string $attendeeName = null, ?string $attendeeEmail = null): EventAttendee
    {
        // Generate unique ticket code
        $ticketCode = $this->ticketGenerator->generateTicketCode();

        // Use provided attendee info or fall back to order customer info
        $attendee = EventAttendee::create([
            'event_order_id' => $order->id,
            'event_ticket_id' => $ticket->id,
            'event_id' => $order->event_id,
            'attendee_name' => $attendeeName ?? $order->customer_name,
            'attendee_email' => $attendeeEmail ?? $order->customer_email,
            'ticket_code' => $ticketCode,
            'price_paid' => $ticket->isDonation() ? 0 : $ticket->price, // Will be updated if donation
            'status' => 'valid',
        ]);

        // Generate QR code
        try {
            $qrCodePath = $this->qrCodeService->generateTicketQrCode($ticketCode, $attendee->id);
            $attendee->update(['qr_code_path' => $qrCodePath]);
        } catch (\Exception $e) {
            // Log error but don't fail the order
            \Log::error('QR code generation failed: ' . $e->getMessage());
        }

        return $attendee;
    }

    /**
     * Complete order after successful payment
     *
     * @param EventOrder $order
     * @param array $paymentData
     * @return void
     */
    public function completeOrder(EventOrder $order, array $paymentData): void
    {
        DB::transaction(function () use ($order, $paymentData) {
            // Update order status
            $order->update([
                'payment_status' => 'completed',
                'payment_method' => $paymentData['payment_method'] ?? 'paystack',
                'payment_reference' => $paymentData['reference'] ?? null,
                'payment_response' => json_encode($paymentData),
                'paid_at' => now(),
                'status' => 'confirmed',
            ]);

            // Update ticket sold counts
            $attendees = $order->attendees;
            $ticketCounts = [];

            foreach ($attendees as $attendee) {
                $ticketId = $attendee->event_ticket_id;
                $ticketCounts[$ticketId] = ($ticketCounts[$ticketId] ?? 0) + 1;
            }

            foreach ($ticketCounts as $ticketId => $count) {
                $ticket = EventTicket::find($ticketId);
                if ($ticket) {
                    $ticket->incrementSold($count);
                }
            }

            // Update event revenue
            $order->event->increment('total_revenue', $order->subtotal);

            // Send confirmation email
            $this->sendTicketConfirmationEmail($order);
        });
    }

    /**
     * Send ticket confirmation email to customer
     *
     * @param EventOrder $order
     * @return void
     */
    public function sendTicketConfirmationEmail(EventOrder $order): void
    {
        try {
            // Load relationships
            $order->load(['event', 'attendees.ticket']);

            // Queue email for background processing (HIGH PRIORITY FIX #21)
            // This prevents 3-5 second delay at checkout
            Mail::to($order->customer_email)->queue(new TicketConfirmationMail($order));
        } catch (\Exception $e) {
            \Log::error('Ticket confirmation email failed: ' . $e->getMessage());
        }
    }

    /**
     * Check ticket availability before purchase
     *
     * @param Event $event
     * @param array $tickets Array of ['ticket_id' => quantity]
     * @return array ['available' => bool, 'errors' => array]
     */
    public function checkTicketAvailability(Event $event, array $tickets): array
    {
        $errors = [];

        foreach ($tickets as $ticketId => $quantity) {
            $ticket = EventTicket::find($ticketId);

            if (!$ticket) {
                $errors[] = "Ticket not found";
                continue;
            }

            if ($ticket->event_id !== $event->id) {
                $errors[] = "Invalid ticket for this event";
                continue;
            }

            if (!$ticket->isAvailable()) {
                $errors[] = "{$ticket->name} is no longer available";
                continue;
            }

            if ($quantity < $ticket->min_per_order) {
                $errors[] = "{$ticket->name}: Minimum {$ticket->min_per_order} tickets required";
                continue;
            }

            if ($quantity > $ticket->max_per_order) {
                $errors[] = "{$ticket->name}: Maximum {$ticket->max_per_order} tickets allowed";
                continue;
            }

            // Check if enough tickets remaining
            if ($ticket->quantity !== null) {
                $remaining = $ticket->quantity - $ticket->sold;
                if ($quantity > $remaining) {
                    $errors[] = "{$ticket->name}: Only {$remaining} tickets remaining";
                }
            }
        }

        return [
            'available' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Calculate order total
     *
     * @param Event $event
     * @param array $tickets Array of ['ticket_id' => quantity]
     * @return array
     */
    public function calculateOrderTotal(Event $event, array $tickets): array
    {
        $subtotal = 0;
        $ticketCount = 0;
        $breakdown = [];

        foreach ($tickets as $ticketId => $quantity) {
            $ticket = EventTicket::findOrFail($ticketId);
            $ticketTotal = $ticket->price * $quantity;
            $subtotal += $ticketTotal;
            $ticketCount += $quantity;

            $breakdown[] = [
                'ticket_id' => $ticketId,
                'ticket_name' => $ticket->name,
                'quantity' => $quantity,
                'unit_price' => $ticket->price,
                'total' => $ticketTotal,
            ];
        }

        // Calculate fees
        $fees = $this->feeCalculator->calculateFees($subtotal, $ticketCount, $event->fee_bearer);

        return array_merge($fees, [
            'ticket_breakdown' => $breakdown,
            'ticket_count' => $ticketCount,
        ]);
    }
}
