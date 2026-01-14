<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'description' => fake()->paragraph(),
            'password' => static::$password ??= Hash::make('password'),
            'is_suspended' => false,
        ];
    }
}
