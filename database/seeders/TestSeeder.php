<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestSeeder extends Seeder
{
    public function run()
    {
        // 1. Create OR Get Company
        $company = DB::table('companies')->where('slug', 'ferrari-events-global')->first();
        if ($company) {
            $companyId = $company->id;
        } else {
            $companyId = DB::table('companies')->insertGetId([
                'name' => 'Ferrari Events Global',
                'slug' => 'ferrari-events-global',
                'email' => 'events@ferrari.com',
                'password' => bcrypt('password'),
                'description' => 'The official organizer of premium luxury events.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Create an Event
        $exists = DB::table('events')->where('slug', 'ferrari-world-tour-2026')->exists();
        if (!$exists) {
            DB::table('events')->insert([
                'company_id' => $companyId,
                'title' => 'Ferrari World Tour 2026',
                'slug' => 'ferrari-world-tour-2026',
                'summary' => 'Experience the power of the Prancing Horse in Accra.',
                'overview' => 'Join us for an exclusive showcase of the latest Ferrari models.',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(5)->addHours(4),
                'venue_name' => 'Independence Square',
                'venue_address' => 'Accra, Ghana',
                'status' => 'approved', // CRITICAL for search
                'location_type' => 'venue',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Test data seeded: Ferrari Events Global + Event');
    }
}
