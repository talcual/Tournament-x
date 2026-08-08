<?php

namespace Tests\Feature\Admin;

use App\Models\Sport;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_team(): void
    {
        $admin = $this->createUserWithRole('admin');
        $sport = Sport::factory()->create();

        $this->actingAs($admin)->post(route('admin.teams.store'), [
            'sport_id' => $sport->id,
            'name' => 'Red Lions',
            'coach_name' => 'Coach A',
        ])->assertRedirect(route('admin.teams.index'));

        $this->assertDatabaseHas('teams', ['name' => 'Red Lions']);
    }

    public function test_admin_can_update_team(): void
    {
        $admin = $this->createUserWithRole('admin');
        $team = Team::factory()->create();

        $this->actingAs($admin)->put(route('admin.teams.update', $team), [
            'sport_id' => $team->sport_id,
            'name' => 'Updated Team',
            'is_active' => false,
        ])->assertRedirect(route('admin.teams.index'));

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Updated Team', 'is_active' => false]);
    }

    public function test_admin_can_delete_team(): void
    {
        $admin = $this->createUserWithRole('admin');
        $team = Team::factory()->create();

        $this->actingAs($admin)->delete(route('admin.teams.destroy', $team))->assertRedirect(route('admin.teams.index'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_required_fields_are_validated(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->post(route('admin.teams.store'), [])
            ->assertSessionHasErrors(['sport_id', 'name']);
    }
}
