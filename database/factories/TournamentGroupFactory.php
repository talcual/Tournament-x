<?php

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\TournamentGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TournamentGroup>
 */
class TournamentGroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        return [
            'tournament_id' => Tournament::factory(),
            'name' => 'Group '.fake()->randomElement($letters),
            'code' => fake()->randomElement($letters),
            'display_order' => fake()->numberBetween(1, 8),
        ];
    }
}
