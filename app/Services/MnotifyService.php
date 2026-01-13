<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MnotifyService
{
    protected $apiKey;
    protected $senderId;
    protected $baseUrl = 'https://api.mnotify.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.mnotify.api_key');
        $this->senderId = config('services.mnotify.sender_id');
    }

    /**
     * Send SMS via mNotify
     *
     * @param string $recipient Phone number (format: 233XXXXXXXXX or 0XXXXXXXXX)
     * @param string $message SMS message content
     * @return array Response with success status and message
     */
    public function sendSMS(string $recipient, string $message): array
    {
        // Format phone number to international format (233XXXXXXXXX)
        $recipient = $this->formatPhoneNumber($recipient);

        try {
            $response = Http::asForm()->post("{$this->baseUrl}/sms/quick", [
                'key' => $this->apiKey,
                'recipient' => [$recipient],
                'sender' => $this->senderId,
                'message' => $message,
                'is_schedule' => 'false',
                'schedule_date' => ''
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === 'success') {
                Log::info("mNotify SMS sent successfully", [
                    'recipient' => $recipient,
                    'response' => $result
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $result
                ];
            }

            Log::error("mNotify SMS failed", [
                'recipient' => $recipient,
                'response' => $result
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send SMS',
                'data' => $result
            ];

        } catch (\Exception $e) {
            Log::error("mNotify SMS exception", [
                'recipient' => $recipient,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to mNotify accepted format (233XXXXXXXXX)
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 233
        if (substr($phone, 0, 1) === '0') {
            $phone = '233' . substr($phone, 1);
        }

        // If doesn't start with 233, add it
        if (substr($phone, 0, 3) !== '233') {
            $phone = '233' . $phone;
        }

        return $phone;
    }

    /**
     * Check account balance
     *
     * @return array
     */
    public function checkBalance(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/balance", [
                'key' => $this->apiKey
            ]);

            $result = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'balance' => $result['balance'] ?? 0,
                    'data' => $result
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to check balance',
                'data' => $result
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Balance check error: ' . $e->getMessage()
            ];
        }
    }
}
