<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Models\SmsCredit;
use App\Models\SmsPlan;
use App\Models\SmsTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
        $this->baseUrl = config('services.paystack.url', 'https://api.paystack.co');
    }

    /**
     * Initialize a payment transaction
     * @param User|Company $owner
     * @param array|null $channels Payment channels (e.g., ['card', 'mobile_money'])
     */
    public function initializePayment(Model $owner, SmsPlan $plan, string $email, ?string $callbackUrl = null, ?array $channels = null): array
    {
        try {
            // Generate unique reference
            $reference = 'SMS_' . strtoupper(Str::random(10)) . '_' . time();

            // Convert amount to kobo (Paystack uses smallest currency unit)
            $amountInKobo = $plan->price * 100;

            // Create transaction record
            $transaction = SmsTransaction::create([
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'sms_plan_id' => $plan->id,
                'reference' => $reference,
                'amount' => $plan->price,
                'credits' => $plan->sms_credits,
                'type' => 'purchase',
                'status' => 'pending',
                'payment_method' => 'paystack',
            ]);

            // Get owner name based on model type
            $ownerName = $owner instanceof Company ? $owner->company_name : $owner->name;
            $ownerType = class_basename(get_class($owner));

            // Prepare payment data
            $paymentData = [
                'email' => $email,
                'amount' => $amountInKobo,
                'reference' => $reference,
                'callback_url' => $callbackUrl ?? route('organization.sms.payment.callback'),
                'metadata' => [
                    'owner_id' => $owner->id,
                    'owner_type' => $ownerType,
                    'owner_name' => $ownerName,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'sms_credits' => $plan->sms_credits,
                    'transaction_id' => $transaction->id,
                ],
            ];

            // Add payment channels if specified
            if ($channels !== null && !empty($channels)) {
                $paymentData['channels'] = $channels;
            }

            // Initialize payment with Paystack
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transaction/initialize", $paymentData);

            $data = $response->json();

            if ($response->successful() && $data['status'] === true) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'access_code' => $data['data']['access_code'],
                    'reference' => $reference,
                    'transaction_id' => $transaction->id,
                ];
            }

            // Update transaction status to failed
            $transaction->update([
                'status' => 'failed',
                'payment_response' => json_encode($data),
            ]);

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Failed to initialize payment',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization failed', [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while initializing payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment transaction
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get("{$this->baseUrl}/transaction/verify/{$reference}");

            $data = $response->json();

            if ($response->successful() && $data['status'] === true) {
                $transactionData = $data['data'];

                // Check if payment was successful
                if ($transactionData['status'] === 'success') {
                    return [
                        'success' => true,
                        'reference' => $reference,
                        'amount' => $transactionData['amount'] / 100, // Convert from kobo
                        'paid_at' => $transactionData['paid_at'],
                        'metadata' => $transactionData['metadata'] ?? [],
                        'response' => $transactionData,
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Payment was not successful. Status: ' . $transactionData['status'],
                    'status' => $transactionData['status'],
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Failed to verify payment',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Paystack payment verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while verifying payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process successful payment and credit SMS balance
     */
    public function processSuccessfulPayment(string $reference): array
    {
        DB::beginTransaction();
        try {
            // Find transaction
            $transaction = SmsTransaction::where('reference', $reference)->first();

            if (!$transaction) {
                return [
                    'success' => false,
                    'error' => 'Transaction not found',
                ];
            }

            // Check if already processed
            if ($transaction->status === 'completed') {
                return [
                    'success' => true,
                    'message' => 'Transaction already processed',
                    'credits_added' => 0,
                ];
            }

            // Verify payment with Paystack
            $verification = $this->verifyPayment($reference);

            if (!$verification['success']) {
                $transaction->update([
                    'status' => 'failed',
                    'payment_response' => json_encode($verification),
                ]);

                DB::commit();

                return [
                    'success' => false,
                    'error' => $verification['error'],
                ];
            }

            // Get or create SMS credit balance
            $creditBalance = SmsCredit::firstOrCreate(
                [
                    'owner_id' => $transaction->owner_id,
                    'owner_type' => $transaction->owner_type
                ],
                ['balance' => 0, 'total_purchased' => 0, 'total_used' => 0]
            );

            // Add credits to balance
            $creditBalance->addCredits($transaction->credits);

            // Update transaction
            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($verification['response']),
                'completed_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'credits_added' => $transaction->credits,
                'new_balance' => $creditBalance->fresh()->balance,
                'transaction_id' => $transaction->id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while processing payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Add manual credits (by admin) - supports both User and Company
     * @param User|Company $owner
     */
    public function addManualCredits($owner, int $credits, ?int $adminId = null, ?string $notes = null): array
    {
        DB::beginTransaction();
        try {
            // Generate unique reference for manual credit
            $reference = 'MANUAL_' . strtoupper(Str::random(10)) . '_' . time();

            // Create transaction record
            $transaction = SmsTransaction::create([
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'reference' => $reference,
                'amount' => 0, // Manual credits have no amount
                'credits' => $credits,
                'type' => 'manual_credit',
                'status' => 'completed',
                'credited_by' => $adminId,
                'notes' => $notes,
                'completed_at' => now(),
            ]);

            // Get or create SMS credit balance
            $creditBalance = SmsCredit::firstOrCreate(
                [
                    'owner_id' => $owner->id,
                    'owner_type' => get_class($owner)
                ],
                ['balance' => 0, 'total_purchased' => 0, 'total_used' => 0]
            );

            // Add credits to balance
            $creditBalance->addCredits($credits);

            DB::commit();

            return [
                'success' => true,
                'credits_added' => $credits,
                'new_balance' => $creditBalance->fresh()->balance,
                'transaction_id' => $transaction->id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual credit addition failed', [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner),
                'credits' => $credits,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while adding manual credits: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Initialize ticket payment (for events)
     * MOBILE MONEY SUPPORT: Ghana's #1 payment method!
     *
     * @param string $email Customer email
     * @param float $amount Total amount
     * @param string $reference Unique order reference
     * @param array $metadata Order metadata
     * @param string|null $callbackUrl Payment callback URL
     * @param string $paymentMethod Payment method: 'card', 'mobile_money', or 'all'
     * @return array
     */
    public function initializeTicketPayment(
        string $email,
        float $amount,
        string $reference,
        array $metadata = [],
        ?string $callbackUrl = null,
        string $paymentMethod = 'all'
    ): array {
        try {
            // Convert amount to kobo (Paystack uses smallest currency unit)
            $amountInKobo = (int) ($amount * 100);

            // Determine payment channels
            $channels = $this->getPaymentChannels($paymentMethod);

            // Prepare payment data
            $paymentData = [
                'email' => $email,
                'amount' => $amountInKobo,
                'reference' => $reference,
                'callback_url' => $callbackUrl ?? route('events.payment.callback'),
                'metadata' => $metadata,
            ];

            // Add payment channels
            if (!empty($channels)) {
                $paymentData['channels'] = $channels;
            }

            Log::info('Initializing Paystack payment', [
                'reference' => $reference,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'channels' => $channels,
            ]);

            // Initialize payment with Paystack
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/transaction/initialize", $paymentData);

            $data = $response->json();

            if ($response->successful() && $data['status'] === true) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'access_code' => $data['data']['access_code'],
                    'reference' => $reference,
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Failed to initialize payment',
                'response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Paystack ticket payment initialization failed', [
                'reference' => $reference,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while initializing payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment channels based on payment method
     *
     * @param string $paymentMethod 'card', 'mobile_money', or 'all'
     * @return array
     */
    protected function getPaymentChannels(string $paymentMethod): array
    {
        return match ($paymentMethod) {
            'card' => ['card'],
            'mobile_money' => ['mobile_money'],
            'bank' => ['bank'],
            'ussd' => ['ussd'],
            'all' => [], // Empty array = all channels enabled
            default => [],
        };
    }

    /**
     * Get available mobile money providers in Ghana
     *
     * @return array
     */
    public function getMobileMoneyProviders(): array
    {
        return [
            [
                'code' => 'mtn',
                'name' => 'MTN Mobile Money',
                'description' => 'Pay with MTN MoMo',
                'icon' => 'mtn-logo.png',
            ],
            [
                'code' => 'vod',
                'name' => 'Vodafone Cash',
                'description' => 'Pay with Vodafone Cash',
                'icon' => 'vodafone-logo.png',
            ],
            [
                'code' => 'tgo',
                'name' => 'AirtelTigo Money',
                'description' => 'Pay with AirtelTigo Money',
                'icon' => 'airteltigo-logo.png',
            ],
        ];
    }

    /**
     * Get Paystack public key
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
