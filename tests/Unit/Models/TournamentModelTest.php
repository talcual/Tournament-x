<?php

namespace Tests\Unit\Models;

use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_is_cast_to_enum(): void
    {
        $t = Tournament::factory()->create(['format' => TournamentFormat::RoundRobin]);
        $t->refresh();

        $this->assertInstanceOf(TournamentFormat::class, $t->format);
        $this->assertSame(TournamentFormat::RoundRobin, $t->format);
    }

    public function test_status_is_cast_to_enum(): void
    {
        $t = Tournament::factory()->create(['status' => TournamentStatus::InProgress]);
        $t->refresh();

        $this->assertInstanceOf(TournamentStatus::class, $t->status);
        $this->assertSame(TournamentStatus::InProgress, $t->status);
    }

    public function test_tournament_belongs_to_sport_and_organizer(): void
    {
        $sport = Sport::factory()->create();
        $organizer = User::factory()->create();
        $t = Tournament::factory()->for($sport, 'sport')->for($organizer, 'organizer')->create();

        $this->assertSame($sport->id, $t->sport->id);
        $this->assertSame($organizer->id, $t->organizer->id);
    }

    public function test_tournament_can_register_a_team(): void
    {
        $tournament = Tournament::factory()->create();
        $team = Team::factory()->create(['sport_id' => $tournament->sport_id]);

        $tournament->registrations()->create([
            'participant_id' => $team->id,
            'participant_type' => $team->getMorphClass(),
            'is_confirmed' => true,
        ]);

        $this->assertCount(1, $tournament->registrations()->get());
        $this->assertEquals($team->id, $tournament->registrations()->first()->participant->id);
    }

    public function test_format_enum_label(): void
    {
        $this->assertSame('Eliminación directa', TournamentFormat::SingleElimination->label());
        $this->assertSame('Round Robin', TournamentFormat::RoundRobin->label());
    }

    public function test_status_enum_color(): void
    {
        $this->assertSame('green', TournamentStatus::InProgress->color());
        $this->assertSame('red', TournamentStatus::Cancelled->color());
    }
}
