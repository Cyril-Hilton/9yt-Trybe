<?php

namespace Database\Seeders;

use App\Models\SmsPlan;
use Illuminate\Database\Seeder;

class SmsPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Pack',
                'description' => 'Perfect for small businesses and startups',
                'sms_credits' => 100,
                'price' => 15.00,
                'badge' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Basic Plan',
                'description' => 'Great for regular SMS campaigns',
                'sms_credits' => 250,
                'price' => 35.00,
                'badge' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'description' => 'Most popular choice for growing businesses',
                'sms_credits' => 500,
                'price' => 65.00,
                'badge' => 'Most Popular',
                'is_active' => true,
            ],
            [
                'name' => 'Business Plan',
                'description' => 'Best value for active marketers',
                'sms_credits' => 1000,
                'price' => 120.00,
                'badge' => 'Best Value',
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'For high-volume SMS campaigns',
                'sms_credits' => 2500,
                'price' => 280.00,
                'badge' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Corporate',
                'description' => 'Ideal for large organizations',
                'sms_credits' => 5000,
                'price' => 520.00,
                'badge' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Maximum reach for enterprise needs',
                'sms_credits' => 10000,
                'price' => 950.00,
                'badge' => 'Enterprise',
                'is_active' => true,
            ],
            [
                'name' => 'Mega Pack',
                'description' => 'Unlimited potential for large-scale operations',
                'sms_credits' => 25000,
                'price' => 2200.00,
                'badge' => 'Ultimate',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SmsPlan::create($plan);
        }

        $this->command->info('SMS Plans seeded successfully!');
    }
}
