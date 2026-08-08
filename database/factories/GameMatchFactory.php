<?php

namespace Database\Factories;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameMatch>
 */
class GameMatchFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'round_id' => null,
            'group_id' => null,
            'venue_id' => null,
            'round_number' => 1,
            'match_number' => fake()->numberBetween(1, 16),
            'bracket_side' => null,
            'bracket_index' => null,
            'scheduled_at' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'status' => MatchStatus::Scheduled,
            'notes' => null,
            'next_match_id' => null,
        ];
    }

    public function finished(): static
    {
        return $this->state(fn () => ['status' => MatchStatus::Finished]);
    }

    public function live(): static
    {
        return $this->state(fn () => ['status' => MatchStatus::Live]);
    }
}
