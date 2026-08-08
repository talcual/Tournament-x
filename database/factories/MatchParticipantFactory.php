<?php

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\MatchParticipant;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatchParticipant>
 */
class MatchParticipantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'match_id' => GameMatch::factory(),
            'participant_id' => Team::factory(),
            'participant_type' => (new Team)->getMorphClass(),
            'side' => fake()->randomElement(['home', 'away', null]),
            'score' => fake()->numberBetween(0, 5),
            'is_winner' => false,
        ];
    }

    public function winner(): static
    {
        return $this->state(fn () => ['is_winner' => true]);
    }
}
