<?php

namespace Tests\Unit\Services;

use App\Enums\ParticipantType;
use App\Enums\TournamentFormat;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use App\Services\BracketGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BracketGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_elimination_generates_correct_number_of_matches_for_power_of_two(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::SingleElimination, 8);

        app(BracketGenerator::class)->generate($tournament);

        $this->assertEquals(4, $tournament->matches()->where('round_number', 1)->count());
        $this->assertEquals(2, $tournament->matches()->where('round_number', 2)->count());
        $this->assertEquals(1, $tournament->matches()->where('round_number', 3)->count());
        $this->assertEquals(7, $tournament->matches()->count());
    }

    public function test_single_elimination_handles_non_power_of_two_with_byes(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::SingleElimination, 6);

        app(BracketGenerator::class)->generate($tournament);

        $this->assertEquals(4, $tournament->matches()->where('round_number', 1)->count());
        $this->assertEquals(2, $tournament->matches()->where('round_number', 2)->count());
        $this->assertEquals(1, $tournament->matches()->where('round_number', 3)->count());
    }

    public function test_round_robin_generates_correct_matches(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::RoundRobin, 4);

        app(BracketGenerator::class)->generate($tournament);

        $this->assertEquals(6, $tournament->matches()->count());
        $this->assertEquals(3, $tournament->rounds()->count());
    }

    public function test_groups_knockout_groups_participants(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::GroupsKnockout, 8);

        app(BracketGenerator::class)->generate($tournament);

        $this->assertGreaterThanOrEqual(2, $tournament->tournamentGroups()->count());
    }

    public function test_double_elimination_creates_winners_losers_and_grand_final(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::DoubleElimination, 4);

        app(BracketGenerator::class)->generate($tournament);

        $this->assertEquals(1, $tournament->matches()->where('bracket_side', 'grand')->count());
        $this->assertGreaterThan(0, $tournament->matches()->where('bracket_side', 'winners')->count());
        $this->assertGreaterThan(0, $tournament->matches()->where('bracket_side', 'losers')->count());
    }

    public function test_generator_throws_with_too_few_participants(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::SingleElimination, 1);

        $this->expectException(\RuntimeException::class);
        app(BracketGenerator::class)->generate($tournament);
    }

    public function test_generator_seeds_standings(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::RoundRobin, 4);

        app(BracketGenerator::class)->generate($tournament);

        $this->assertEquals(4, $tournament->standings()->count());
    }

    public function test_generator_links_matches_with_next_match_id(): void
    {
        $tournament = $this->makeTournament(TournamentFormat::SingleElimination, 4);

        app(BracketGenerator::class)->generate($tournament);

        $r1MatchWithNext = $tournament->matches()->where('round_number', 1)->whereNotNull('next_match_id')->count();
        $this->assertGreaterThan(0, $r1MatchWithNext);

        $final = $tournament->matches()->where('round_number', 2)->first();
        $this->assertNull($final->next_match_id);
    }

    private function makeTournament(TournamentFormat $format, int $participantCount): Tournament
    {
        $sport = Sport::factory()->create();
        $organizer = User::factory()->create();
        $tournament = Tournament::factory()
            ->for($sport, 'sport')
            ->for($organizer, 'organizer')
            ->create(['format' => $format, 'participant_type' => ParticipantType::Team]);

        $teams = Team::factory()->count($participantCount)->create(['sport_id' => $sport->id]);
        foreach ($teams as $team) {
            $tournament->registrations()->create([
                'participant_id' => $team->id,
                'participant_type' => $team->getMorphClass(),
                'is_confirmed' => true,
            ]);
        }

        return $tournament;
    }
}
