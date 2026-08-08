<?php

namespace Database\Factories;

use App\Models\Round;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Round>
 */
class RoundFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'group_id' => null,
            'name' => 'Round '.fake()->numberBetween(1, 6),
            'number' => fake()->numberBetween(1, 6),
            'starts_at' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'ends_at' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'is_knockout' => false,
        ];
    }

    public function knockout(): static
    {
        return $this->state(fn () => ['is_knockout' => true]);
    }
}
