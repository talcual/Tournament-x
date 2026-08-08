<?php

namespace Database\Factories;

use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'sport_id' => Sport::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->bothify('##??'),
            'logo_path' => null,
            'home_venue' => fake()->city(),
            'coach_name' => fake()->name(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
