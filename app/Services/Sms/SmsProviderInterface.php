<?php

namespace App\Services\Sms;

interface SmsProviderInterface
{
    /**
     * Send a single SMS message
     *
     * @param string $recipient Phone number
     * @param string $message Message content
     * @param string|null $senderId Sender ID
     * @return array ['success' => bool, 'message_id' => string|null, 'error' => string|null, 'response' => array]
     */
    public function sendSms(string $recipient, string $message, ?string $senderId = null): array;

    /**
     * Send bulk SMS messages
     *
     * @param array $recipients Array of phone numbers
     * @param string $message Message content
     * @param string|null $senderId Sender ID
     * @return array ['success' => bool, 'total' => int, 'sent' => int, 'failed' => int, 'results' => array]
     */
    public function sendBulkSms(array $recipients, string $message, ?string $senderId = null): array;

    /**
     * Schedule SMS messages for future delivery
     *
     * @param array $recipients Array of phone numbers
     * @param string $message Message content
     * @param string $scheduledAt Date/time in provider format
     * @param string|null $senderId Sender ID
     * @return array ['success' => bool, 'message_id' => string|null, 'error' => string|null, 'response' => array]
     */
    public function sendScheduledSms(array $recipients, string $message, string $scheduledAt, ?string $senderId = null): array;

    /**
     * Check SMS delivery status
     *
     * @param string $messageId External message ID
     * @return array ['status' => string, 'delivered_at' => string|null]
     */
    public function checkDeliveryStatus(string $messageId): array;

    /**
     * Calculate SMS credits needed for a message
     *
     * @param string $message Message content
     * @return int Number of SMS credits required
     */
    public function calculateCredits(string $message): int;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getProviderName(): string;
}
