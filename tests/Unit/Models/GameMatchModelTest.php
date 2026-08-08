<?php

namespace Tests\Unit\Models;

use App\Enums\MatchStatus;
use App\Models\GameMatch;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameMatchModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_is_cast_to_enum(): void
    {
        $m = GameMatch::factory()->finished()->create();
        $m->refresh();

        $this->assertInstanceOf(MatchStatus::class, $m->status);
        $this->assertSame(MatchStatus::Finished, $m->status);
    }

    public function test_match_belongs_to_tournament(): void
    {
        $tournament = Tournament::factory()->create();
        $match = GameMatch::factory()->create(['tournament_id' => $tournament->id]);

        $this->assertSame($tournament->id, $match->tournament->id);
    }
}
