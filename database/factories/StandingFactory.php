<?php

namespace Database\Factories;

use App\Models\Standing;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Standing>
 */
class StandingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'group_id' => null,
            'participant_id' => Team::factory(),
            'participant_type' => (new Team)->getMorphClass(),
            'played' => 0,
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'points' => 0,
            'position' => null,
        ];
    }
}
