<?php

namespace Tests\Unit\Models;

use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_uses_unique_slug(): void
    {
        $sport = Sport::factory()->create();
        $a = Team::factory()->create(['sport_id' => $sport->id, 'name' => 'SameName']);

        $this->assertNotEmpty($a->slug);
        $this->assertDatabaseMissing('teams', ['name' => $a->name, 'slug' => $a->slug, 'id' => $a->id + 1]);
    }

    public function test_player_full_name_attribute(): void
    {
        $player = Player::factory()->create(['first_name' => 'Ada', 'last_name' => 'Lovelace']);
        $this->assertSame('Ada Lovelace', $player->full_name);
    }

    public function test_player_team_relation(): void
    {
        $team = Team::factory()->create();
        $player = Player::factory()->create(['team_id' => $team->id]);

        $this->assertSame($team->id, $player->team->id);
    }
}
