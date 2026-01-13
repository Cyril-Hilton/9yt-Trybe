<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Company;
use App\Models\SmsCredit;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    /**
     * Seed the admin account and setup platform SMS credits
     *
     * Admin Login:
     * Email: 9yttrybe@gmail.com
     * Password: Justbe999!
     */
    public function run(): void
    {
        // Create or update admin account
        $admin = Admin::updateOrCreate(
            ['email' => '9yttrybe@gmail.com'],
            [
                'name' => '9yt !Trybe Admin',
                'email' => '9yttrybe@gmail.com',
                'password' => Hash::make('Justbe999!'),
                'is_super_admin' => true,
            ]
        );

        $this->command->info('✅ Admin account created/updated:');
        $this->command->info('   Email: 9yttrybe@gmail.com');
        $this->command->info('   Password: Justbe999!');
        $this->command->info('');

        // Get or create platform company (ID: 1) for SMS credits
        $platformCompany = Company::first();

        if ($platformCompany) {
            // Create or update SMS credit balance for platform company
            $smsCredit = SmsCredit::updateOrCreate(
                [
                    'owner_id' => $platformCompany->id,
                    'owner_type' => Company::class,
                ],
                [
                    'balance' => 1000, // Start with 1000 free credits for testing
                    'total_purchased' => 1000,
                    'total_used' => 0,
                ]
            );

            $this->command->info('✅ Platform SMS credits initialized:');
            $this->command->info('   Company: ' . $platformCompany->name);
            $this->command->info('   Credits: ' . $smsCredit->balance);
            $this->command->info('');
        } else {
            $this->command->warn('⚠️  No company found. Create a company first, then run this seeder again.');
        }

        $this->command->info('🎉 Setup complete! You can now:');
        $this->command->info('   1. Login at /admin using the credentials above');
        $this->command->info('   2. Receive admin notifications at 9yttrybe@gmail.com');
        $this->command->info('   3. Send SMS notifications (1000 credits available)');
        $this->command->info('');
        $this->command->info('📝 IMPORTANT: Update .env with valid Mnotify API key for SMS to work:');
        $this->command->info('   MNOTIFY_API_KEY=your_real_api_key_here');
    }
}
