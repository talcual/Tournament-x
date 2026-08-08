<?php

namespace Tests\Feature\Admin;

use App\Models\Sport;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_tournaments(): void
    {
        $admin = $this->createUserWithRole('admin');
        $sport = Sport::factory()->create();
        $organizer = User::factory()->create();
        Tournament::factory()->count(3)->for($sport, 'sport')->for($organizer, 'organizer')->create();

        $this->actingAs($admin)
            ->get(route('admin.tournaments.index'))
            ->assertOk()
            ->assertSee('Tournaments');
    }

    public function test_admin_can_create_a_tournament(): void
    {
        $admin = $this->createUserWithRole('admin');
        $sport = Sport::factory()->create();
        $venue = Venue::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.tournaments.store'), [
            'sport_id' => $sport->id,
            'organizer_id' => $admin->id,
            'name' => 'Spring Cup',
            'description' => 'Spring tournament.',
            'format' => 'single_elimination',
            'status' => 'registration',
            'participant_type' => 'team',
            'min_participants' => 4,
            'max_participants' => 16,
            'starts_at' => now()->addMonth()->format('Y-m-d'),
            'ends_at' => now()->addMonths(2)->format('Y-m-d'),
            'venues' => [$venue->id],
        ]);

        $response->assertRedirect(route('admin.tournaments.index'));
        $this->assertDatabaseHas('tournaments', ['name' => 'Spring Cup']);
    }

    public function test_admin_can_update_a_tournament(): void
    {
        $admin = $this->createUserWithRole('admin');
        $tournament = Tournament::factory()->for(Sport::factory(), 'sport')->for(User::factory(), 'organizer')->create();

        $this->actingAs($admin)->put(route('admin.tournaments.update', $tournament), [
            'sport_id' => $tournament->sport_id,
            'organizer_id' => $tournament->organizer_id,
            'name' => 'Updated name',
            'format' => 'round_robin',
            'status' => 'in_progress',
            'participant_type' => 'team',
            'min_participants' => 2,
        ])->assertRedirect(route('admin.tournaments.index'));

        $this->assertDatabaseHas('tournaments', ['id' => $tournament->id, 'name' => 'Updated name', 'format' => 'round_robin']);
    }

    public function test_admin_can_delete_a_tournament(): void
    {
        $admin = $this->createUserWithRole('admin');
        $tournament = Tournament::factory()->for(Sport::factory(), 'sport')->for(User::factory(), 'organizer')->create();

        $this->actingAs($admin)
            ->delete(route('admin.tournaments.destroy', $tournament))
            ->assertRedirect(route('admin.tournaments.index'));

        $this->assertDatabaseMissing('tournaments', ['id' => $tournament->id]);
    }

    public function test_validation_fails_for_missing_required_fields(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.tournaments.store'), [])
            ->assertSessionHasErrors(['name', 'format', 'status', 'sport_id', 'min_participants', 'participant_type']);
    }
}
