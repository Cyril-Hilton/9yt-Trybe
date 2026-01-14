<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventTicket>
 */
class EventTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->randomElement(['Regular Admission', 'VIP Access', 'Early Bird', 'Student Pass']),
            'description' => fake()->sentence(),
            'type' => 'paid',
            'price' => fake()->randomFloat(2, 10, 500),
            'quantity' => fake()->numberBetween(50, 500),
            'sold' => 0,
            'min_per_order' => 1,
            'max_per_order' => 10,
            'sales_start' => now(),
            'sales_end' => now()->addMonths(1),
            'is_active' => true,
            'is_hidden' => false,
            'order' => 1,
        ];
    }

    public function free()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'free',
                'price' => 0,
            ];
        });
    }
}
