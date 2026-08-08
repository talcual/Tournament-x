<?php

namespace Database\Factories;

use App\Enums\ParticipantType;
use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use App\Models\Sport;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true).' Tournament';

        return [
            'sport_id' => Sport::factory(),
            'organizer_id' => User::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->bothify('##??'),
            'description' => fake()->paragraph(),
            'format' => TournamentFormat::SingleElimination,
            'status' => TournamentStatus::Draft,
            'max_participants' => fake()->randomElement([8, 16, 32]),
            'min_participants' => 2,
            'starts_at' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'ends_at' => fake()->dateTimeBetween('+3 weeks', '+4 months'),
            'registration_deadline' => fake()->dateTimeBetween('+1 day', '+1 week'),
            'participant_type' => ParticipantType::Team,
            'is_featured' => false,
        ];
    }

    public function registration(): static
    {
        return $this->state(fn () => ['status' => TournamentStatus::Registration]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => TournamentStatus::InProgress]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => TournamentStatus::Completed]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function individual(): static
    {
        return $this->state(fn () => ['participant_type' => ParticipantType::Player]);
    }
}
