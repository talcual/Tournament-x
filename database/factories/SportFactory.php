<?php

namespace Database\Factories;

use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Sport>
 */
class SportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->word()).' Sport';

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->bothify('##??'),
            'icon' => null,
            'description' => fake()->sentence(),
            'is_team_sport' => true,
            'points_per_win' => 3,
            'points_per_draw' => 1,
            'points_per_loss' => 0,
            'allows_draws' => true,
        ];
    }

    public function individual(): static
    {
        return $this->state(fn () => ['is_team_sport' => false]);
    }
}
