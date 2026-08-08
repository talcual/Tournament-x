<?php

namespace Tests\Feature\Admin;

use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_player(): void
    {
        $admin = $this->createUserWithRole('admin');
        $sport = Sport::factory()->create();

        $this->actingAs($admin)->post(route('admin.players.store'), [
            'sport_id' => $sport->id,
            'first_name' => 'Roger',
            'last_name' => 'Federer',
            'nationality' => 'CH',
            'ranking' => 1,
        ])->assertRedirect(route('admin.players.index'));

        $this->assertDatabaseHas('players', ['first_name' => 'Roger', 'last_name' => 'Federer']);
    }

    public function test_admin_can_update_player(): void
    {
        $admin = $this->createUserWithRole('admin');
        $player = Player::factory()->create(['first_name' => 'Before']);

        $this->actingAs($admin)->put(route('admin.players.update', $player), [
            'sport_id' => $player->sport_id,
            'first_name' => 'After',
            'last_name' => $player->last_name,
        ])->assertRedirect(route('admin.players.index'));

        $this->assertDatabaseHas('players', ['id' => $player->id, 'first_name' => 'After']);
    }

    public function test_admin_can_delete_player(): void
    {
        $admin = $this->createUserWithRole('admin');
        $player = Player::factory()->create();

        $this->actingAs($admin)->delete(route('admin.players.destroy', $player))->assertRedirect(route('admin.players.index'));

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }

    public function test_player_can_have_team(): void
    {
        $admin = $this->createUserWithRole('admin');
        $team = Team::factory()->create();

        $this->actingAs($admin)->post(route('admin.players.store'), [
            'sport_id' => $team->sport_id,
            'team_id' => $team->id,
            'first_name' => 'On',
            'last_name' => 'Team',
        ])->assertRedirect(route('admin.players.index'));

        $player = Player::where('first_name', 'On')->first();
        $this->assertEquals($team->id, $player->team_id);
    }
}
