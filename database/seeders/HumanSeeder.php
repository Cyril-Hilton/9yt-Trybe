<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventTicket;

use Illuminate\Support\Str;

class HumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌍 Starting Human Seeder...');

        // 1. Create Users (Attendees)
        $users = User::factory(20)->create();
        $this->command->info('👤 Created 20 Users.');

        // 2. Create Companies (Organizers)
        $companies = Company::factory(5)->create();
        $this->command->info('🏢 Created 5 Companies.');

        // 3. Create Events & Tickets for each Company
        foreach ($companies as $company) {
            // Create 3-6 events per company
            $events = Event::factory(rand(3, 6))->create([
                'company_id' => $company->id,
            ]);

            foreach ($events as $event) {
                // Create 2-4 ticket types per event
                EventTicket::factory(rand(2, 4))->create([
                    'event_id' => $event->id,
                ]);
            }
        }
        $this->command->info('🎉 Created ' . Event::count() . ' Events and ' . EventTicket::count() . ' Ticket Types.');

        // 4. Create News Articles
        for ($i = 0; $i < 10; $i++) {
            \App\Models\Article::create([
                'title' => fake()->sentence(),
                'slug' => Str::slug(fake()->sentence()),
                'description' => fake()->paragraph(),
                'content' => fake()->paragraphs(3, true),
                'source_name' => '9yt !Trybe News',
                'source_url' => 'https://9yttrybe.com',
                'author' => fake()->name(),
                'is_published' => true,
                'published_at' => now()->subDays(rand(0, 10)),
            ]);
        }
        $this->command->info('📰 Created 10 News Articles.');
        
        $this->command->info('✅ Human Seeding Complete! The Ferrari Engine is fueled with data.');
    }
}
