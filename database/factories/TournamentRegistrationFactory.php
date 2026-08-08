<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TournamentRegistration>
 */
class TournamentRegistrationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'participant_id' => Team::factory(),
            'participant_type' => (new Team)->getMorphClass(),
            'seed' => null,
            'is_confirmed' => true,
        ];
    }

    public function individual(): static
    {
        return $this->state(fn () => [
            'participant_id' => Player::factory(),
            'participant_type' => (new Player)->getMorphClass(),
        ]);
    }

    public function unconfirmed(): static
    {
        return $this->state(fn () => ['is_confirmed' => false]);
    }
}
