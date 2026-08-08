<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Player>
 */
class PlayerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $first = fake()->firstName();
        $last = fake()->lastName();
        $slug = Str::slug("$first-$last").'-'.fake()->unique()->bothify('##??');

        return [
            'sport_id' => Sport::factory(),
            'team_id' => Team::factory(),
            'first_name' => $first,
            'last_name' => $last,
            'slug' => $slug,
            'birth_date' => fake()->dateTimeBetween('-40 years', '-16 years'),
            'nationality' => fake()->countryCode(),
            'ranking' => fake()->numberBetween(1, 500),
            'rating' => fake()->numberBetween(1000, 2800),
            'photo_path' => null,
            'is_active' => true,
        ];
    }

    public function independent(): static
    {
        return $this->state(fn () => ['team_id' => null]);
    }
}
