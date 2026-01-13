<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MnotifyProvider implements SmsProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.mnotify.com/api';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.mnotify.api_key', '');
    }

    /**
     * Send a single SMS message
     */
    public function sendSms(string $recipient, string $message, ?string $senderId = null): array
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/sms/quick", [
                'key' => $this->apiKey,
                'recipient' => [$this->formatPhoneNumber($recipient)],
                'sender' => $senderId ?? config('services.mnotify.sender_id', 'MNOTIFY'),
                'message' => $message,
                'is_schedule' => false,
                'schedule_date' => '',
            ]);

            $data = $response->json();

            // Log API response for debugging
            Log::info('Mnotify API Response', [
                'status_code' => $response->status(),
                'response' => $data,
                'recipient' => $recipient,
            ]);

            if ($response->successful() && isset($data['code']) && $data['code'] === '2000') {
                return [
                    'success' => true,
                    'message_id' => $data['message_id'] ?? null,
                    'error' => null,
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'message_id' => null,
                'error' => $data['message'] ?? $data['summary'] ?? 'Failed to send SMS. Please check your Mnotify API credentials.',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Mnotify SMS sending failed', [
                'error' => $e->getMessage(),
                'recipient' => $recipient,
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage(),
                'response' => [],
            ];
        }
    }

    /**
     * Send bulk SMS messages
     */
    public function sendBulkSms(array $recipients, string $message, ?string $senderId = null): array
    {
        $formattedRecipients = array_map([$this, 'formatPhoneNumber'], $recipients);
        $totalRecipients = count($formattedRecipients);

        try {
            $response = Http::timeout(60)->post("{$this->baseUrl}/sms/quick", [
                'key' => $this->apiKey,
                'recipient' => $formattedRecipients,
                'sender' => $senderId ?? config('services.mnotify.sender_id', 'MNOTIFY'),
                'message' => $message,
                'is_schedule' => false,
                'schedule_date' => '',
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['code']) && $data['code'] === '2000') {
                return [
                    'success' => true,
                    'total' => $totalRecipients,
                    'sent' => $totalRecipients,
                    'failed' => 0,
                    'results' => [
                        'message_id' => $data['message_id'] ?? null,
                        'response' => $data,
                    ],
                ];
            }

            return [
                'success' => false,
                'total' => $totalRecipients,
                'sent' => 0,
                'failed' => $totalRecipients,
                'results' => [
                    'error' => $data['message'] ?? 'Failed to send bulk SMS',
                    'response' => $data,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Mnotify bulk SMS sending failed', [
                'error' => $e->getMessage(),
                'total_recipients' => $totalRecipients,
            ]);

            return [
                'success' => false,
                'total' => $totalRecipients,
                'sent' => 0,
                'failed' => $totalRecipients,
                'results' => [
                    'error' => $e->getMessage(),
                ],
            ];
        }
    }

    /**
     * Check SMS delivery status
     */
    public function checkDeliveryStatus(string $messageId): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/sms/status", [
                'key' => $this->apiKey,
                'message_id' => $messageId,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['code']) && $data['code'] === '2000') {
                $status = $this->mapDeliveryStatus($data['status'] ?? 'unknown');

                return [
                    'status' => $status,
                    'delivered_at' => $status === 'delivered' ? ($data['delivered_at'] ?? now()->toDateTimeString()) : null,
                    'response' => $data,
                ];
            }

            return [
                'status' => 'unknown',
                'delivered_at' => null,
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Mnotify delivery status check failed', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
            ]);

            return [
                'status' => 'unknown',
                'delivered_at' => null,
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Calculate SMS credits needed for a message
     */
    public function calculateCredits(string $message): int
    {
        $length = mb_strlen($message);

        // Standard GSM encoding: 160 chars per SMS
        // Unicode encoding: 70 chars per SMS
        $isUnicode = $this->containsUnicode($message);

        if ($isUnicode) {
            if ($length <= 70) return 1;
            return (int) ceil($length / 67); // Multipart Unicode: 67 chars per part
        }

        if ($length <= 160) return 1;
        return (int) ceil($length / 153); // Multipart GSM: 153 chars per part
    }

    /**
     * Get provider name
     */
    public function getProviderName(): string
    {
        return 'Mnotify';
    }

    /**
     * Format phone number for Mnotify (add Ghana country code if missing)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any spaces, dashes, or parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // If it starts with 0, replace with 233 (Ghana)
        if (substr($phone, 0, 1) === '0') {
            return '233' . substr($phone, 1);
        }

        // If it doesn't start with country code, add Ghana code
        if (!preg_match('/^233/', $phone) && !preg_match('/^\+/', $phone)) {
            return '233' . $phone;
        }

        // Remove + if present
        return str_replace('+', '', $phone);
    }

    /**
     * Check if message contains Unicode characters
     */
    protected function containsUnicode(string $message): bool
    {
        return preg_match('/[^\x00-\x7F]/', $message) === 1;
    }

    /**
     * Map Mnotify status to our standard status
     */
    protected function mapDeliveryStatus(string $status): string
    {
        return match (strtolower($status)) {
            'delivered' => 'delivered',
            'submitted', 'sent' => 'submitted',
            'failed', 'undelivered' => 'failed',
            'rejected' => 'rejected',
            'expired' => 'expired',
            default => 'pending',
        };
    }

    /**
     * Send scheduled SMS (for future implementation)
     */
    public function sendScheduledSms(array $recipients, string $message, string $scheduledAt, ?string $senderId = null): array
    {
        $formattedRecipients = array_map([$this, 'formatPhoneNumber'], $recipients);

        try {
            $response = Http::timeout(60)->post("{$this->baseUrl}/sms/quick", [
                'key' => $this->apiKey,
                'recipient' => $formattedRecipients,
                'sender' => $senderId ?? config('services.mnotify.sender_id', 'MNOTIFY'),
                'message' => $message,
                'is_schedule' => true,
                'schedule_date' => $scheduledAt, // Format: YYYY-MM-DD HH:MM:SS
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['code']) && $data['code'] === '2000') {
                return [
                    'success' => true,
                    'message_id' => $data['message_id'] ?? null,
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Failed to schedule SMS',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Mnotify scheduled SMS failed', [
                'error' => $e->getMessage(),
                'scheduled_at' => $scheduledAt,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => [],
            ];
        }
    }

    /**
     * Register a sender ID with Mnotify
     */
    public function registerSenderId(string $senderName, string $purpose): array
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/senderid/register", [
                'key' => $this->apiKey,
                'sender_name' => $senderName,
                'purpose' => $purpose,
            ]);

            $data = $response->json();

            Log::info('Mnotify Sender ID Registration Response', [
                'sender_name' => $senderName,
                'status_code' => $response->status(),
                'response' => $data,
            ]);

            if ($response->successful() && isset($data['code']) && $data['code'] === '2000') {
                return [
                    'success' => true,
                    'message' => $data['message'] ?? 'Sender ID registered successfully',
                    'status' => $data['summary']['status'] ?? 'Pending',
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Failed to register sender ID',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Mnotify sender ID registration failed', [
                'error' => $e->getMessage(),
                'sender_name' => $senderName,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => [],
            ];
        }
    }

    /**
     * Check sender ID status with Mnotify
     */
    public function checkSenderIdStatus(string $senderName): array
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/senderid/status", [
                'key' => $this->apiKey,
                'sender_name' => $senderName,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['code']) && $data['code'] === '2000') {
                return [
                    'success' => true,
                    'status' => $data['summary']['status'] ?? 'Unknown',
                    'sender_name' => $data['summary']['sender name'] ?? $senderName,
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Failed to check sender ID status',
                'status' => 'Unknown',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Mnotify sender ID status check failed', [
                'error' => $e->getMessage(),
                'sender_name' => $senderName,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'Unknown',
                'response' => [],
            ];
        }
    }
}
