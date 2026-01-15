<?php

namespace App\Console\Commands;

use App\Services\PaystackService;
use App\Services\Sms\MnotifyProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestPaymentSystems extends Command
{
    protected $signature = 'test:payment-systems';
    protected $description = 'Test all payment systems and API integrations';

    public function handle()
    {
        $this->info('🔍 Testing Payment Systems & API Integrations...');
        $this->newLine();

        $allPassed = true;

        // Test 1: Paystack Configuration
        $allPassed = $this->testPaystackConfiguration() && $allPassed;
        $this->newLine();

        // Test 2: Paystack API Connectivity
        $allPassed = $this->testPaystackConnectivity() && $allPassed;
        $this->newLine();

        // Test 3: Mnotify Configuration
        $allPassed = $this->testMnotifyConfiguration() && $allPassed;
        $this->newLine();

        // Test 4: Database Tables
        $allPassed = $this->testDatabaseTables() && $allPassed;
        $this->newLine();

        // Test 5: Email Configuration
        $allPassed = $this->testEmailConfiguration() && $allPassed;
        $this->newLine();

        // Test 6: Maps Configuration
        $allPassed = $this->testMapsConfiguration() && $allPassed;
        $this->newLine();

        // Final Summary
        if ($allPassed) {
            $this->info('✅ ALL TESTS PASSED - System is ready for payments!');
        } else {
            $this->error('❌ SOME TESTS FAILED - Please review errors above');
        }

        return $allPassed ? 0 : 1;
    }

    protected function testPaystackConfiguration(): bool
    {
        $this->info('1️⃣  Testing Paystack Configuration...');

        $publicKey = config('services.paystack.public_key');
        $secretKey = config('services.paystack.secret_key');
        $baseUrl = config('services.paystack.url');

        $passed = true;

        // Check public key
        if (empty($publicKey) || strlen($publicKey) < 10) {
            $this->error('   ❌ Paystack public key not configured or invalid');
            $this->warn('   Set PAYSTACK_PUBLIC_KEY in .env file');
            $passed = false;
        } else {
            $this->info('   ✅ Paystack public key configured');
        }

        // Check secret key
        if (empty($secretKey) || strlen($secretKey) < 10) {
            $this->error('   ❌ Paystack secret key not configured or invalid');
            $this->warn('   Set PAYSTACK_SECRET_KEY in .env file');
            $passed = false;
        } else {
            $this->info('   ✅ Paystack secret key configured');
        }

        // Check base URL
        if ($baseUrl === 'https://api.paystack.co') {
            $this->info('   ✅ Paystack base URL correct');
        } else {
            $this->warn('   ⚠️  Paystack base URL: ' . $baseUrl);
        }

        return $passed;
    }

    protected function testPaystackConnectivity(): bool
    {
        $this->info('2️⃣  Testing Paystack API Connectivity...');

        $secretKey = config('services.paystack.secret_key');

        if (empty($secretKey) || strlen($secretKey) < 10) {
            $this->warn('   ⏭️  Skipping - Paystack not configured');
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->timeout(10)->get('https://api.paystack.co/transaction/verify/invalid-ref-test');

            if ($response->status() === 404) {
                // 404 means API is reachable and authenticated (just reference not found)
                $this->info('   ✅ Paystack API is reachable');
                $this->info('   ✅ Paystack authentication working');
                return true;
            } elseif ($response->status() === 401) {
                $this->error('   ❌ Paystack authentication failed - Invalid secret key');
                return false;
            } else {
                $this->info('   ✅ Paystack API is reachable (Status: ' . $response->status() . ')');
                return true;
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Cannot connect to Paystack API');
            $this->warn('   Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function testMnotifyConfiguration(): bool
    {
        $this->info('3️⃣  Testing Mnotify SMS Configuration...');

        $apiKey = config('services.mnotify.api_key');
        $senderId = config('services.mnotify.sender_id');

        $passed = true;

        if (empty($apiKey)) {
            $this->warn('   ⚠️  Mnotify API key not configured');
            $this->warn('   Set MNOTIFY_API_KEY in .env file');
            $passed = false;
        } else {
            $this->info('   ✅ Mnotify API key configured');
        }

        if (empty($senderId)) {
            $this->warn('   ⚠️  Mnotify sender ID not configured');
            $this->warn('   Set MNOTIFY_SENDER_ID in .env file');
        } else {
            $this->info('   ✅ Mnotify sender ID configured: ' . $senderId);
        }

        return $passed;
    }

    protected function testDatabaseTables(): bool
    {
        $this->info('4️⃣  Testing Database Tables...');

        $tables = [
            'shop_orders' => 'Shop orders',
            'shop_order_items' => 'Shop order items',
            'event_orders' => 'Event orders',
            'event_attendees' => 'Event attendees',
            'sms_transactions' => 'SMS transactions',
            'sms_credits' => 'SMS credits',
            'sms_campaigns' => 'SMS campaigns',
            'sms_messages' => 'SMS messages',
        ];

        $passed = true;

        foreach ($tables as $table => $description) {
            try {
                \DB::table($table)->limit(1)->count();
                $this->info("   ✅ Table '{$table}' exists");
            } catch (\Exception $e) {
                $this->error("   ❌ Table '{$table}' missing");
                $this->warn("   Run: php artisan migrate");
                $passed = false;
            }
        }

        // Check shop_orders has session_id column
        try {
            \DB::table('shop_orders')->select('session_id')->limit(1)->get();
            $this->info("   ✅ shop_orders.session_id column exists");
        } catch (\Exception $e) {
            $this->error("   ❌ shop_orders.session_id column missing");
            $this->warn("   Run: php artisan migrate");
            $passed = false;
        }

        return $passed;
    }

    protected function testEmailConfiguration(): bool
    {
        $this->info('5️⃣  Testing Email Configuration...');

        $mailer = config('mail.mailers.smtp');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $passed = true;

        if (empty($mailer['host'])) {
            $this->warn('   ⚠️  SMTP host not configured');
            $this->warn('   Set MAIL_HOST in .env file');
            $passed = false;
        } else {
            $this->info('   ✅ SMTP host configured: ' . $mailer['host']);
        }

        if (empty($fromAddress)) {
            $this->warn('   ⚠️  Mail from address not set');
            $this->warn('   Set MAIL_FROM_ADDRESS in .env file');
            $passed = false;
        } else {
            $this->info('   ✅ Mail from address: ' . $fromAddress);
        }

        $this->info('   ℹ️  Mail from name: ' . $fromName);

        return $passed;
    }

    protected function testMapsConfiguration(): bool
    {
        $this->info('6️⃣  Testing Maps Configuration...');

        $enabled = config('services.maps.enabled', true);
        $provider = config('services.maps.provider', 'osm');
        $googleKey = config('services.google.maps_api_key');

        $this->info('   ℹ️  Maps Enabled: ' . ($enabled ? 'Yes' : 'No'));
        $this->info('   ℹ️  Maps Provider: ' . strtoupper($provider));

        if (!$enabled) {
            $this->warn('   ⚠️  Map functionality is DISABLED globally');
            return true;
        }

        if ($provider === 'google') {
            if (empty($googleKey)) {
                $this->error('   ❌ Google Maps API key not configured');
                $this->warn('   Set GOOGLE_MAPS_API_KEY in .env file');
                return false;
            }
            $this->info('   ✅ Google Maps API key configured');
        } else {
            $this->info('   ✅ OpenStreetMap/Leaflet provider active (no API key required)');
        }

        return true;
    }
}
