<?php

namespace Tests\Unit\Services;

use App\Enums\MatchStatus;
use App\Enums\ParticipantType;
use App\Enums\TournamentFormat;
use App\Models\GameMatch;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use App\Services\BracketGenerator;
use App\Services\FinishMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinishMatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_finishing_match_updates_standings_with_sport_scoring(): void
    {
        $sport = Sport::factory()->create([
            'points_per_win' => 3,
            'points_per_draw' => 1,
            'points_per_loss' => 0,
            'allows_draws' => true,
        ]);
        $tournament = $this->tournament($sport, TournamentFormat::RoundRobin);
        $matches = $tournament->matches()->with('participants')->get();
        $first = $matches[0];
        $first->participants[0]->update(['score' => 2]);
        $first->participants[1]->update(['score' => 1]);

        $winnerKey = $first->participants[0]->participant_type.':'.$first->participants[0]->participant_id;
        $results = [];
        foreach ($first->participants as $p) {
            $results[] = [
                'participant_id' => $p->participant_id,
                'participant_type' => $p->participant_type,
                'score' => $p->score,
            ];
        }

        $service = app(FinishMatch::class);
        $service->execute($first, $results, $first->participants[0]->participant_id);

        $this->assertSame(MatchStatus::Finished, $first->refresh()->status);

        $winner = $tournament->standings()
            ->where('participant_id', $first->participants[0]->participant_id)
            ->first();
        $loser = $tournament->standings()
            ->where('participant_id', $first->participants[1]->participant_id)
            ->first();

        $this->assertSame(1, $winner->played);
        $this->assertSame(1, $winner->wins);
        $this->assertSame(3, $winner->points);
        $this->assertSame(2, $winner->goals_for);
        $this->assertSame(1, $winner->goals_against);

        $this->assertSame(1, $loser->played);
        $this->assertSame(1, $loser->losses);
        $this->assertSame(0, $loser->points);
    }

    public function test_finishing_draw_increments_draws_points(): void
    {
        $sport = Sport::factory()->create([
            'points_per_win' => 3,
            'points_per_draw' => 1,
            'points_per_loss' => 0,
            'allows_draws' => true,
        ]);
        $tournament = $this->tournament($sport, TournamentFormat::RoundRobin);
        $match = $tournament->matches()->with('participants')->first();
        $match->participants[0]->update(['score' => 1]);
        $match->participants[1]->update(['score' => 1]);

        $results = $match->participants->map(fn ($p) => [
            'participant_id' => $p->participant_id,
            'participant_type' => $p->participant_type,
            'score' => $p->score,
        ])->values()->all();

        app(FinishMatch::class)->execute($match, $results, null, isDraw: true);

        $playedIds = collect($results)->pluck('participant_id');
        foreach ($tournament->standings()->whereIn('participant_id', $playedIds)->get() as $row) {
            $this->assertSame(1, $row->played);
            $this->assertSame(1, $row->draws);
            $this->assertSame(1, $row->points);
        }
    }

    public function test_sport_without_draws_throws_when_trying_to_record_draw(): void
    {
        $sport = Sport::factory()->create([
            'points_per_win' => 1,
            'points_per_draw' => 0,
            'points_per_loss' => 0,
            'allows_draws' => false,
        ]);
        $tournament = $this->tournament($sport, TournamentFormat::RoundRobin);
        $match = $tournament->matches()->with('participants')->first();
        $match->participants[0]->update(['score' => 1]);
        $match->participants[1]->update(['score' => 1]);

        $results = $match->participants->map(fn ($p) => [
            'participant_id' => $p->participant_id,
            'participant_type' => $p->participant_type,
            'score' => $p->score,
        ])->values()->all();

        $this->expectException(\RuntimeException::class);
        app(FinishMatch::class)->execute($match, $results, null, isDraw: true);
    }

    public function test_finishing_match_propagates_winner_to_next_match(): void
    {
        $sport = Sport::factory()->create();
        $tournament = $this->tournament($sport, TournamentFormat::SingleElimination, 4);
        $r1Match = $tournament->matches()->where('round_number', 1)->with('participants')->first();

        $results = $r1Match->participants->map(fn ($p) => [
            'participant_id' => $p->participant_id,
            'participant_type' => $p->participant_type,
            'score' => 2,
        ])->values()->all();
        $results[1]['score'] = 0;

        app(FinishMatch::class)->execute($r1Match, $results, $r1Match->participants[0]->participant_id);

        $nextMatch = GameMatch::find($r1Match->next_match_id);
        $this->assertEquals(1, $nextMatch->participants()->count());
        $this->assertSame($r1Match->participants[0]->participant_id, $nextMatch->participants()->first()->participant_id);
    }

    private function tournament(Sport $sport, TournamentFormat $format, int $count = 4): Tournament
    {
        $organizer = User::factory()->create();
        $tournament = Tournament::factory()
            ->for($sport, 'sport')
            ->for($organizer, 'organizer')
            ->create(['format' => $format, 'participant_type' => ParticipantType::Team]);

        $teams = Team::factory()->count($count)->create(['sport_id' => $sport->id]);
        foreach ($teams as $team) {
            $tournament->registrations()->create([
                'participant_id' => $team->id,
                'participant_type' => $team->getMorphClass(),
                'is_confirmed' => true,
            ]);
        }

        app(BracketGenerator::class)->generate($tournament);

        return $tournament->refresh();
    }
}
