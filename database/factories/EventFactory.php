<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate = (clone $startDate)->modify('+4 hours');

        return [
            'company_id' => Company::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(8),
            'summary' => fake()->sentence(10),
            'overview' => fake()->paragraphs(3, true),
            'event_type' => fake()->randomElement(['conference', 'concert', 'workshop', 'party']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'timezone' => 'Africa/Accra',
            'location_type' => 'venue',
            'venue_name' => fake()->city() . ' Conference Center',
            'venue_address' => fake()->address(),
            'venue_latitude' => fake()->latitude(5.5, 5.7),
            'venue_longitude' => fake()->longitude(-0.3, -0.1),
            'status' => 'approved',
            'views_count' => fake()->numberBetween(0, 1000),
            'likes_count' => fake()->numberBetween(0, 500),
            'tickets_sold' => 0,
            'total_revenue' => 0,
            'approved_at' => now(),
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'approved_at' => null,
            ];
        });
    }
}
