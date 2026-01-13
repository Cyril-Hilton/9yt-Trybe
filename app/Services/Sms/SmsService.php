<?php

namespace App\Services\Sms;

use App\Models\Company;
use App\Models\User;
use App\Models\SmsCampaign;
use App\Models\SmsContact;
use App\Models\SmsCredit;
use App\Models\SmsMessage;
use App\Models\SmsSenderId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SmsService
{
    protected SmsProviderInterface $provider;

    public function __construct(?SmsProviderInterface $provider = null)
    {
        $this->provider = $provider ?? $this->getDefaultProvider();
    }

    /**
     * Get the default SMS provider based on configuration
     */
    protected function getDefaultProvider(): SmsProviderInterface
    {
        $providerName = config('services.sms.default_provider', 'mnotify');

        return match ($providerName) {
            'mnotify' => new MnotifyProvider(),
            // 'hubtel' => new HubtelProvider(), // Future implementation
            default => new MnotifyProvider(),
        };
    }

    /**
     * Send a single SMS
     * @param User|Company $owner
     */
    public function sendSingleSms(Model $owner, string $recipient, string $message, ?string $senderId = null): array
    {
        // Calculate credits needed
        $creditsNeeded = $this->provider->calculateCredits($message);

        // Check if owner has enough credits
        $creditBalance = $this->getCreditBalance($owner);
        if ($creditBalance->balance < $creditsNeeded) {
            return [
                'success' => false,
                'error' => 'Insufficient SMS credits. Please purchase more credits.',
                'credits_needed' => $creditsNeeded,
                'credits_available' => $creditBalance->balance,
            ];
        }

        // Get sender ID
        $senderIdToUse = $this->getSenderId($owner, $senderId);

        DB::beginTransaction();
        try {
            // Create campaign
            $campaign = SmsCampaign::create([
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'name' => 'Single SMS - ' . now()->format('Y-m-d H:i:s'),
                'message' => $message,
                'sender_id' => $senderIdToUse,
                'type' => 'single',
                'status' => 'processing',
                'total_recipients' => 1,
                'total_sent' => 0,
                'credits_used' => $creditsNeeded,
            ]);

            // Auto-save recipient to contacts database
            $this->autoSaveRecipientsToContacts($owner, [$recipient], $campaign->name);

            // Send SMS
            $result = $this->provider->sendSms($recipient, $message, $senderIdToUse);

            // Create message record
            $smsMessage = SmsMessage::create([
                'sms_campaign_id' => $campaign->id,
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'recipient' => $recipient,
                'message' => $message,
                'sender_id' => $senderIdToUse,
                'status' => $result['success'] ? 'submitted' : 'failed',
                'credits_used' => $creditsNeeded,
                'external_id' => $result['message_id'] ?? null,
                'api_response' => $result['response'] ?? null,
                'sent_at' => $result['success'] ? now() : null,
            ]);

            if ($result['success']) {
                // Deduct credits
                $creditBalance->deductCredits($creditsNeeded);

                // Update campaign
                $campaign->update([
                    'status' => 'completed',
                    'total_sent' => 1,
                    'total_delivered' => 1,
                    'sent_at' => now(),
                    'completed_at' => now(),
                ]);
            } else {
                $campaign->update([
                    'status' => 'failed',
                    'sent_at' => now(),
                    'completed_at' => now(),
                ]);
            }

            DB::commit();

            return [
                'success' => $result['success'],
                'campaign_id' => $campaign->id,
                'message_id' => $smsMessage->id,
                'credits_used' => $creditsNeeded,
                'credits_remaining' => $creditBalance->fresh()->balance,
                'error' => $result['error'] ?? null,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMS sending failed', [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while sending SMS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send bulk SMS
     */
    public function sendBulkSms(Model $owner, array $recipients, string $message, ?string $senderId = null, ?string $campaignName = null): array
    {
        // Remove duplicates and empty values
        $recipients = array_unique(array_filter($recipients));
        $totalRecipients = count($recipients);

        if ($totalRecipients === 0) {
            return [
                'success' => false,
                'error' => 'No valid recipients provided.',
            ];
        }

        // Calculate credits needed
        $creditsPerMessage = $this->provider->calculateCredits($message);
        $totalCreditsNeeded = $creditsPerMessage * $totalRecipients;

        // Check if company has enough credits
        $creditBalance = $this->getCreditBalance($owner);
        if ($creditBalance->balance < $totalCreditsNeeded) {
            return [
                'success' => false,
                'error' => 'Insufficient SMS credits. Please purchase more credits.',
                'credits_needed' => $totalCreditsNeeded,
                'credits_available' => $creditBalance->balance,
                'recipients_count' => $totalRecipients,
            ];
        }

        // Get sender ID
        $senderIdToUse = $this->getSenderId($owner, $senderId);

        DB::beginTransaction();
        try {
            // Create campaign
            $campaign = SmsCampaign::create([
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'name' => $campaignName ?? 'Bulk SMS - ' . now()->format('Y-m-d H:i:s'),
                'message' => $message,
                'sender_id' => $senderIdToUse,
                'type' => 'bulk',
                'status' => 'processing',
                'total_recipients' => $totalRecipients,
                'total_sent' => 0,
                'credits_used' => $totalCreditsNeeded,
                'sent_at' => now(),
            ]);

            // Auto-save recipients to contacts database
            $this->autoSaveRecipientsToContacts($owner, $recipients, $campaign->name);

            // Send bulk SMS
            $result = $this->provider->sendBulkSms($recipients, $message, $senderIdToUse);

            // Create message records for each recipient
            $sentCount = 0;
            foreach ($recipients as $recipient) {
                SmsMessage::create([
                    'sms_campaign_id' => $campaign->id,
                    'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                    'recipient' => $recipient,
                    'message' => $message,
                    'sender_id' => $senderIdToUse,
                    'status' => $result['success'] ? 'submitted' : 'failed',
                    'credits_used' => $creditsPerMessage,
                    'external_id' => $result['message_id'] ?? null,
                    'api_response' => json_encode($result['response'] ?? []),
                    'sent_at' => $result['success'] ? now() : null,
                ]);

                if ($result['success']) {
                    $sentCount++;
                }
            }

            if ($result['success']) {
                // Deduct credits
                $creditBalance->deductCredits($totalCreditsNeeded);

                // Update campaign
                $campaign->update([
                    'status' => 'completed',
                    'total_sent' => $sentCount,
                    'total_delivered' => $sentCount,
                    'completed_at' => now(),
                ]);
            } else {
                $campaign->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                ]);
            }

            DB::commit();

            return [
                'success' => $result['success'],
                'campaign_id' => $campaign->id,
                'total_recipients' => $totalRecipients,
                'sent_count' => $sentCount,
                'credits_used' => $totalCreditsNeeded,
                'credits_remaining' => $creditBalance->fresh()->balance,
                'error' => $result['results']['error'] ?? null,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk SMS sending failed', [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while sending bulk SMS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send personalized bulk SMS where each recipient gets a different message
     * Used for Excel uploads with column placeholders
     */
    public function sendPersonalizedBulkSms(Model $owner, array $recipients, array $personalizedMessages, ?string $senderId = null, ?string $campaignName = null): array
    {
        // Remove duplicates
        $recipients = array_unique($recipients);
        $totalRecipients = count($recipients);

        if ($totalRecipients === 0) {
            return [
                'success' => false,
                'error' => 'No valid recipients provided.',
            ];
        }

        // Calculate total credits needed (each message may have different length)
        $totalCreditsNeeded = 0;
        $creditsPerRecipient = [];

        foreach ($recipients as $recipient) {
            $message = $personalizedMessages[$recipient] ?? '';
            $credits = $this->provider->calculateCredits($message);
            $creditsPerRecipient[$recipient] = $credits;
            $totalCreditsNeeded += $credits;
        }

        // Check if owner has enough credits
        $creditBalance = $this->getCreditBalance($owner);
        if ($creditBalance->balance < $totalCreditsNeeded) {
            return [
                'success' => false,
                'error' => 'Insufficient SMS credits. Please purchase more credits.',
                'credits_needed' => $totalCreditsNeeded,
                'credits_available' => $creditBalance->balance,
                'recipients_count' => $totalRecipients,
            ];
        }

        // Get sender ID
        $senderIdToUse = $this->getSenderId($owner, $senderId);

        DB::beginTransaction();
        try {
            // Create campaign (use first message as template for display)
            $firstMessage = reset($personalizedMessages);
            $campaign = SmsCampaign::create([
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'name' => $campaignName ?? 'Personalized SMS - ' . now()->format('Y-m-d H:i:s'),
                'message' => $firstMessage . ' (Personalized for each recipient)',
                'sender_id' => $senderIdToUse,
                'type' => 'bulk',
                'status' => 'processing',
                'total_recipients' => $totalRecipients,
                'total_sent' => 0,
                'credits_used' => $totalCreditsNeeded,
                'sent_at' => now(),
            ]);

            // Auto-save recipients to contacts database
            $this->autoSaveRecipientsToContacts($owner, $recipients, $campaign->name);

            // Send personalized SMS to each recipient individually
            $sentCount = 0;
            $failedCount = 0;

            foreach ($recipients as $recipient) {
                $personalizedMessage = $personalizedMessages[$recipient];
                $creditsForMessage = $creditsPerRecipient[$recipient];

                // Send individual SMS
                $result = $this->provider->sendSms($recipient, $personalizedMessage, $senderIdToUse);

                // Create message record
                SmsMessage::create([
                    'sms_campaign_id' => $campaign->id,
                    'owner_id' => $owner->id,
                    'owner_type' => get_class($owner),
                    'recipient' => $recipient,
                    'message' => $personalizedMessage,
                    'sender_id' => $senderIdToUse,
                    'status' => $result['success'] ? 'submitted' : 'failed',
                    'credits_used' => $creditsForMessage,
                    'external_id' => $result['message_id'] ?? null,
                    'api_response' => json_encode($result['response'] ?? []),
                    'sent_at' => $result['success'] ? now() : null,
                ]);

                if ($result['success']) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            // Deduct credits for sent messages only
            $creditsUsed = $sentCount > 0 ? array_sum(array_slice($creditsPerRecipient, 0, $sentCount)) : 0;
            if ($creditsUsed > 0) {
                $creditBalance->deductCredits($creditsUsed);
            }

            // Update campaign
            $campaign->update([
                'status' => $sentCount > 0 ? 'completed' : 'failed',
                'total_sent' => $sentCount,
                'total_delivered' => $sentCount,
                'credits_used' => $creditsUsed,
                'completed_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => $sentCount > 0,
                'campaign_id' => $campaign->id,
                'total_recipients' => $totalRecipients,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'credits_used' => $creditsUsed,
                'credits_remaining' => $creditBalance->fresh()->balance,
                'error' => $failedCount > 0 ? "$failedCount message(s) failed to send" : null,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Personalized bulk SMS sending failed', [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while sending personalized SMS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Schedule SMS campaign
     */
    public function scheduleSms(Model $owner, array $recipients, string $message, string $scheduledAt, ?string $senderId = null, ?string $campaignName = null): array
    {
        // Remove duplicates and empty values
        $recipients = array_unique(array_filter($recipients));
        $totalRecipients = count($recipients);

        if ($totalRecipients === 0) {
            return [
                'success' => false,
                'error' => 'No valid recipients provided.',
            ];
        }

        // Calculate credits needed
        $creditsPerMessage = $this->provider->calculateCredits($message);
        $totalCreditsNeeded = $creditsPerMessage * $totalRecipients;

        // Check if company has enough credits
        $creditBalance = $this->getCreditBalance($owner);
        if ($creditBalance->balance < $totalCreditsNeeded) {
            return [
                'success' => false,
                'error' => 'Insufficient SMS credits. Please purchase more credits.',
                'credits_needed' => $totalCreditsNeeded,
                'credits_available' => $creditBalance->balance,
            ];
        }

        // Normalize scheduled date format for provider
        $scheduledAtFormatted = Carbon::parse($scheduledAt)->format('Y-m-d H:i:s');

        // Get sender ID
        $senderIdToUse = $this->getSenderId($owner, $senderId);

        DB::beginTransaction();
        try {
            // Create scheduled campaign
            $campaign = SmsCampaign::create([
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'name' => $campaignName ?? 'Scheduled SMS - ' . $scheduledAtFormatted,
                'message' => $message,
                'sender_id' => $senderIdToUse,
                'type' => 'bulk',
                'status' => 'scheduled',
                'total_recipients' => $totalRecipients,
                'total_sent' => 0,
                'credits_used' => $totalCreditsNeeded,
                'scheduled_at' => $scheduledAtFormatted,
            ]);

            // Create pending message records
            foreach ($recipients as $recipient) {
                SmsMessage::create([
                    'sms_campaign_id' => $campaign->id,
                    'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                    'recipient' => $recipient,
                    'message' => $message,
                    'sender_id' => $senderIdToUse,
                    'status' => 'scheduled',
                    'credits_used' => $creditsPerMessage,
                ]);
            }

            // Schedule with provider
            $providerResult = $this->provider->sendScheduledSms(
                $recipients,
                $message,
                $scheduledAtFormatted,
                $senderIdToUse
            );

            if (!$providerResult['success']) {
                throw new \RuntimeException($providerResult['error'] ?? 'Failed to schedule SMS with provider.');
            }

            // Reserve credits
            $creditBalance->deductCredits($totalCreditsNeeded);

            DB::commit();

            return [
                'success' => true,
                'campaign_id' => $campaign->id,
                'total_recipients' => $totalRecipients,
                'scheduled_at' => $scheduledAtFormatted,
                'credits_reserved' => $totalCreditsNeeded,
                'credits_remaining' => $creditBalance->fresh()->balance,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMS scheduling failed', [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while scheduling SMS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Import contacts from CSV data
     */
    public function importContacts(Model $owner, array $contactsData): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $ownerType = get_class($owner);

        DB::beginTransaction();
        try {
            foreach ($contactsData as $index => $contact) {
                // Validate phone number
                if (empty($contact['phone_number'])) {
                    $skipped++;
                    $errors[] = "Row {$index}: Missing phone number";
                    continue;
                }

                // Check if contact already exists
                $existing = SmsContact::where('owner_id', $owner->id)
                    ->where('owner_type', $ownerType)
                    ->where('phone_number', $contact['phone_number'])
                    ->exists();

                if ($existing) {
                    $skipped++;
                    continue;
                }

                // Create contact
                SmsContact::create([
                    'owner_id' => $owner->id,
                    'owner_type' => $ownerType,
                    'phone_number' => $contact['phone_number'],
                    'name' => $contact['name'] ?? null,
                    'email' => $contact['email'] ?? null,
                    'group' => $contact['group'] ?? null,
                ]);

                $imported++;
            }

            DB::commit();

            return [
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contact import failed', [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while importing contacts: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Parse contacts from text input (newline or comma separated)
     */
    public function parseContactsFromText(string $text): array
    {
        // Split by newline or comma
        $lines = preg_split('/[\n,]+/', $text);
        $contacts = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Extract phone number (remove non-numeric characters except +)
            $phone = preg_replace('/[^\d+]/', '', $line);

            if (!empty($phone)) {
                $contacts[] = $phone;
            }
        }

        return array_unique($contacts);
    }

    /**
     * Automatically save SMS recipients to contacts database
     * Helps build the contacts database from campaigns
     */
    protected function autoSaveRecipientsToContacts(Model $owner, array $recipients, string $campaignName): int
    {
        $saved = 0;
        $groupName = 'Auto-saved - ' . now()->format('M d, Y');

        foreach ($recipients as $phoneNumber) {
            // Check if contact already exists
            $existing = \App\Models\SmsContact::where('owner_id', $owner->id)
                ->where('owner_type', get_class($owner))
                ->where('phone_number', $phoneNumber)
                ->exists();

            if (!$existing) {
                try {
                    \App\Models\SmsContact::create([
                        'owner_id' => $owner->id,
                        'owner_type' => get_class($owner),
                        'phone_number' => $phoneNumber,
                        'name' => null,
                        'email' => null,
                        'group' => $groupName,
                        'notes' => 'Auto-saved from campaign: ' . $campaignName,
                    ]);
                    $saved++;
                } catch (\Exception $e) {
                    // Silently skip if there's an issue (duplicate race condition, etc.)
                    \Log::warning('Failed to auto-save contact', [
                        'phone' => $phoneNumber,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $saved;
    }

    /**
     * Get or create SMS credit balance for owner (User or Company)
     * @param User|Company $owner
     */
    public function getCreditBalance(Model $owner): SmsCredit
    {
        return SmsCredit::firstOrCreate(
            [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner)
            ],
            ['balance' => 0, 'total_purchased' => 0, 'total_used' => 0]
        );
    }

    /**
     * Get sender ID for owner (User or Company)
     * @param User|Company $owner
     */
    protected function getSenderId(Model $owner, ?string $requestedSenderId = null): string
    {
        if ($requestedSenderId) {
            // Check if the requested sender ID is approved for this owner
            $senderIdRecord = SmsSenderId::where('owner_id', $owner->id)
                ->where('owner_type', get_class($owner))
                ->where('sender_id', $requestedSenderId)
                ->where('status', 'approved')
                ->first();

            if ($senderIdRecord) {
                return $requestedSenderId;
            }
        }

        // Get default sender ID for owner
        $defaultSenderId = SmsSenderId::where('owner_id', $owner->id)
            ->where('owner_type', get_class($owner))
            ->where('status', 'approved')
            ->where('is_default', true)
            ->first();

        if ($defaultSenderId) {
            return $defaultSenderId->sender_id;
        }

        // Fallback to any approved sender ID
        $anySenderId = SmsSenderId::where('owner_id', $owner->id)
            ->where('owner_type', get_class($owner))
            ->where('status', 'approved')
            ->first();

        if ($anySenderId) {
            return $anySenderId->sender_id;
        }

        // Use system default
        return config('services.mnotify.sender_id', 'MNOTIFY');
    }

    /**
     * Calculate credits needed for a message
     */
    public function calculateCredits(string $message): int
    {
        return $this->provider->calculateCredits($message);
    }

    /**
     * Update message delivery status
     */
    public function updateDeliveryStatus(SmsMessage $message): bool
    {
        if (!$message->external_id) {
            return false;
        }

        $result = $this->provider->checkDeliveryStatus($message->external_id);

        if ($result['status'] !== 'unknown') {
            $message->update([
                'status' => $result['status'],
                'delivered_at' => $result['delivered_at'],
            ]);

            // Update campaign statistics
            if ($result['status'] === 'delivered') {
                $message->campaign->increment('total_delivered');
            }

            return true;
        }

        return false;
    }
}
