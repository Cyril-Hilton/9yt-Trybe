<?php

namespace App\Services;

use App\Models\Company;
use App\Models\EventOrder;
use App\Models\ShopOrder;
use App\Services\Sms\SmsService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketConfirmationMail;
use App\Mail\ComplementaryTicketMail;
use App\Mail\ShopOrderConfirmation;
use App\Models\EventAttendee;

class NotificationService
{
    protected SmsService $smsService;
    protected EmailService $emailService;

    public function __construct(SmsService $smsService, EmailService $emailService)
    {
        $this->smsService = $smsService;
        $this->emailService = $emailService;
    }

    /**
     * Send notifications for a single complementary ticket
     * Works with individual EventAttendee record
     *
     * @param EventAttendee $attendee
     * @return array
     */
    public function sendComplementaryTicketNotifications(EventAttendee $attendee, bool $sendSms = true): array
    {
        $results = [
            'email_sent' => false,
            'sms_sent' => false,
            'errors' => [],
        ];

        // Load relationships
        $attendee->load(['event', 'ticket']);

        // Send email immediately to avoid missing queue workers
        try {
            Mail::to($attendee->attendee_email)->send(new ComplementaryTicketMail($attendee));
            $results['email_sent'] = true;
        } catch (\Exception $e) {
            Log::error('Complementary ticket email failed: ' . $e->getMessage(), [
                'attendee_id' => $attendee->id,
                'error' => $e->getMessage(),
            ]);
            $results['errors'][] = 'Email failed: ' . $e->getMessage();
        }

        if (!$sendSms) {
            $results['sms_skipped'] = true;
            return $results;
        }

        // Send SMS
        // For complementary tickets, order is null, so check attendee_phone directly
        $phoneNumber = $attendee->attendee_phone ?? ($attendee->order ? $attendee->order->customer_phone : null);

        Log::info('Attempting to send complementary ticket SMS', [
            'attendee_id' => $attendee->id,
            'phone_number' => $phoneNumber,
            'attendee_phone' => $attendee->attendee_phone,
        ]);

        if ($phoneNumber) {
            try {
                $smsMessage = $this->buildComplementaryTicketSmsMessage($attendee);

                Log::info('Built SMS message for complementary ticket', [
                    'attendee_id' => $attendee->id,
                    'message_length' => strlen($smsMessage),
                ]);

                $results['sms_result'] = $this->sendSmsToCustomer($phoneNumber, $smsMessage);
                $results['sms_sent'] = $results['sms_result']['success'] ?? false;

                Log::info('SMS send result for complementary ticket', [
                    'attendee_id' => $attendee->id,
                    'success' => $results['sms_sent'],
                    'result' => $results['sms_result'],
                ]);

                if (!$results['sms_sent']) {
                    $errorMsg = 'SMS failed: ' . ($results['sms_result']['error'] ?? 'Unknown error');
                    $results['errors'][] = $errorMsg;
                    Log::warning('Complementary ticket SMS not sent', [
                        'attendee_id' => $attendee->id,
                        'error' => $errorMsg,
                        'full_result' => $results['sms_result'],
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Complementary ticket SMS exception: ' . $e->getMessage(), [
                    'attendee_id' => $attendee->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $results['errors'][] = 'SMS failed: ' . $e->getMessage();
            }
        } else {
            Log::warning('No phone number for complementary ticket SMS', [
                'attendee_id' => $attendee->id,
            ]);
            $results['errors'][] = 'No phone number provided for SMS notification';
        }

        return $results;
    }

    /**
     * Send ticket notifications (both SMS and email)
     * Works for free, paid, and complementary tickets
     *
     * @param EventOrder $order
     * @return array
     */
    public function sendTicketNotifications(EventOrder $order): array
    {
        $results = [
            'email_sent' => false,
            'sms_sent' => false,
            'errors' => [],
        ];

        // Load relationships
        $order->load(['event', 'attendees.ticket']);

        // Queue Email for background processing (HIGH PRIORITY FIX #21)
        try {
            Mail::to($order->customer_email)->queue(new TicketConfirmationMail($order));
            $results['email_sent'] = true;
        } catch (\Exception $e) {
            Log::error('Ticket confirmation email failed: ' . $e->getMessage());
            $results['errors'][] = 'Email failed: ' . $e->getMessage();
        }

        // Send SMS
        if ($order->customer_phone) {
            try {
                $smsMessage = $this->buildTicketSmsMessage($order);
                $results['sms_result'] = $this->sendSmsToCustomer($order->customer_phone, $smsMessage);
                $results['sms_sent'] = $results['sms_result']['success'] ?? false;

                if (!$results['sms_sent']) {
                    $results['errors'][] = 'SMS failed: ' . ($results['sms_result']['error'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                Log::error('Ticket SMS notification failed: ' . $e->getMessage());
                $results['errors'][] = 'SMS failed: ' . $e->getMessage();
            }
        } else {
            $results['errors'][] = 'No phone number provided for SMS notification';
        }

        return $results;
    }

    /**
     * Send shop order notifications (both SMS and email)
     *
     * @param ShopOrder $order
     * @return array
     */
    public function sendShopOrderNotifications(ShopOrder $order): array
    {
        $results = [
            'email_sent' => false,
            'sms_sent' => false,
            'errors' => [],
        ];

        // Load relationships
        $order->load('items.product');

        // Queue Email for background processing (HIGH PRIORITY FIX #21)
        try {
            Mail::to($order->customer_email)->queue(new ShopOrderConfirmation($order));
            $results['email_sent'] = true;
        } catch (\Exception $e) {
            Log::error('Shop order confirmation email failed: ' . $e->getMessage());
            $results['errors'][] = 'Email failed: ' . $e->getMessage();
        }

        // Send SMS
        if ($order->customer_phone) {
            try {
                $smsMessage = $this->buildShopOrderSmsMessage($order);
                $results['sms_result'] = $this->sendSmsToCustomer($order->customer_phone, $smsMessage);
                $results['sms_sent'] = $results['sms_result']['success'] ?? false;

                if (!$results['sms_sent']) {
                    $results['errors'][] = 'SMS failed: ' . ($results['sms_result']['error'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                Log::error('Shop order SMS notification failed: ' . $e->getMessage());
                $results['errors'][] = 'SMS failed: ' . $e->getMessage();
            }
        } else {
            $results['errors'][] = 'No phone number provided for SMS notification';
        }

        return $results;
    }

    /**
     * Build SMS message for complementary ticket
     *
     * @param EventAttendee $attendee
     * @return string
     */
    protected function buildComplementaryTicketSmsMessage(EventAttendee $attendee): string
    {
        $eventTitle = $attendee->event->title;
        $ticketCode = $attendee->ticket_code;
        $ticketType = $attendee->ticket->name;

        // Create URL for ticket view
        $ticketUrl = route('user.tickets');

        $message = "You've received a COMPLEMENTARY ticket for '{$eventTitle}'!\n\n";
        $message .= "Ticket Type: {$ticketType}\n";
        $message .= "Ticket Code: {$ticketCode}\n";
        $message .= "Name: {$attendee->attendee_name}\n\n";

        if ($attendee->event->start_date) {
            $message .= "Date: {$attendee->event->start_date->format('M j, Y @ g:i A')}\n";
        }

        if ($attendee->event->location_type === 'venue' && $attendee->event->venue_name) {
            $message .= "Venue: {$attendee->event->venue_name}\n";
        }

        $message .= "\nView your ticket and QR code at:\n{$ticketUrl}\n\n";
        $message .= "Present this ticket code or QR code at the event entrance. See you there!";

        return $message;
    }

    /**
     * Build SMS message for ticket orders
     *
     * @param EventOrder $order
     * @return string
     */
    protected function buildTicketSmsMessage(EventOrder $order): string
    {
        $ticketCount = $order->attendees->count();
        $ticketWord = $ticketCount === 1 ? 'ticket' : 'tickets';
        $eventTitle = $order->event->title;
        $orderNumber = $order->order_number;

        // Determine ticket type
        $ticketType = $order->total == 0 ? 'FREE' : 'PAID';

        // Create URL for ticket download
        $downloadUrl = route('events.order.confirmation', $orderNumber);

        // Build ticket details
        $ticketDetails = [];
        foreach ($order->attendees as $attendee) {
            $ticketDetails[] = "{$attendee->ticket->name} - {$attendee->attendee_name}";
        }
        $ticketList = implode(', ', $ticketDetails);

        $message = "Your {$ticketType} {$ticketWord} for '{$eventTitle}' have been confirmed!\n\n";
        $message .= "Order #: {$orderNumber}\n";
        $message .= "Tickets: {$ticketList}\n\n";
        $message .= "Download your tickets and view details at:\n{$downloadUrl}\n\n";
        $message .= "Show this at the event entrance. Enjoy!";

        return $message;
    }

    /**
     * Build SMS message for shop orders
     *
     * @param ShopOrder $order
     * @return string
     */
    protected function buildShopOrderSmsMessage(ShopOrder $order): string
    {
        $orderNumber = $order->order_number;
        $total = number_format($order->total, 2);
        $itemCount = $order->items->count();
        $itemWord = $itemCount === 1 ? 'item' : 'items';

        // Create URL for order details
        $orderUrl = route('shop.order.confirmation', $orderNumber);

        // Build item details
        $itemDetails = [];
        foreach ($order->items->take(3) as $item) {
            $itemDetails[] = "{$item->quantity}x {$item->product_name}";
        }
        $itemList = implode(', ', $itemDetails);

        if ($itemCount > 3) {
            $remaining = $itemCount - 3;
            $itemList .= " and {$remaining} more";
        }

        $message = "Your shop order has been confirmed!\n\n";
        $message .= "Order #: {$orderNumber}\n";
        $message .= "Items: {$itemList}\n";
        $message .= "Total: GH₵ {$total}\n\n";
        $message .= "Shipping to: {$order->shipping_address}\n\n";
        $message .= "View full order details and track shipping at:\n{$orderUrl}\n\n";
        $message .= "Thank you for shopping with us!";

        return $message;
    }

    /**
     * Send SMS using the platform's default company account
     *
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    protected function sendSmsToCustomer(string $phoneNumber, string $message): array
    {
        try {
            // Get the platform's default company for SMS credits
            // Using company_id = 1 as the default platform account
            $company = Company::find(1);

            if (!$company) {
                Log::warning('Platform SMS company (ID: 1) not found, attempting to use first company');
                $company = Company::first();

                if (!$company) {
                    return [
                        'success' => false,
                        'error' => 'No company account found for SMS platform',
                    ];
                }
            }

            // Ensure SMS credit record exists
            $creditBalance = $this->smsService->getCreditBalance($company);

            if (!$creditBalance || $creditBalance->balance <= 0) {
                Log::warning("Insufficient SMS credits for company {$company->id}. Balance: " . ($creditBalance ? $creditBalance->balance : 0));
                return [
                    'success' => false,
                    'error' => 'Insufficient SMS credits in platform account. Current balance: ' . ($creditBalance ? $creditBalance->balance : 0),
                ];
            }

            // Send SMS using sender ID from config
            $senderId = config('services.mnotify.sender_id', 'MNOTIFY');

            Log::info('Sending SMS with sender ID', [
                'sender_id' => $senderId,
                'sender_id_length' => strlen($senderId),
                'sender_id_raw' => json_encode($senderId),
            ]);

            $result = $this->smsService->sendSingleSms(
                $company,
                $phoneNumber,
                $message,
                $senderId
            );

            return $result;
        } catch (\Exception $e) {
            Log::error('SMS sending error: ' . $e->getMessage(), [
                'phone' => $phoneNumber,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
