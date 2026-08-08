<?php

namespace Tests\Feature\Admin;

use App\Models\Sport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SportManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_sport(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->post(route('admin.sports.store'), [
            'name' => 'Padel',
            'icon' => '🎾',
            'is_team_sport' => true,
            'points_per_win' => 3,
            'points_per_draw' => 1,
            'points_per_loss' => 0,
        ])->assertRedirect(route('admin.sports.index'));

        $this->assertDatabaseHas('sports', ['name' => 'Padel']);
    }

    public function test_admin_can_update_sport(): void
    {
        $admin = $this->createUserWithRole('admin');
        $sport = Sport::factory()->create(['name' => 'Old']);

        $this->actingAs($admin)->put(route('admin.sports.update', $sport), [
            'name' => 'New',
            'is_team_sport' => false,
            'points_per_win' => 2,
            'points_per_draw' => 1,
            'points_per_loss' => 0,
        ])->assertRedirect(route('admin.sports.index'));

        $this->assertDatabaseHas('sports', ['id' => $sport->id, 'name' => 'New', 'is_team_sport' => false]);
    }

    public function test_duplicate_sport_name_is_rejected(): void
    {
        $admin = $this->createUserWithRole('admin');
        Sport::factory()->create(['name' => 'Tennis']);

        $this->actingAs($admin)->post(route('admin.sports.store'), [
            'name' => 'Tennis',
        ])->assertSessionHasErrors('name');
    }
}
