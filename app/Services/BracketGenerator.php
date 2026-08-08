<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Enums\TournamentFormat;
use App\Models\GameMatch;
use App\Models\MatchParticipant;
use App\Models\Round;
use App\Models\Standing;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BracketGenerator
{
    public function generate(Tournament $tournament): Tournament
    {
        $this->wipeExistingStructure($tournament);

        $participants = $this->resolveParticipants($tournament);

        if ($participants->count() < 2) {
            throw new \RuntimeException(__('app.admin.draw.insufficient', ['min' => 2, 'count' => $participants->count()]));
        }

        return DB::transaction(function () use ($tournament, $participants) {
            $tournament->load('sport');

            $format = $tournament->format;

            return match ($format) {
                TournamentFormat::SingleElimination => $this->singleElimination($tournament, $participants),
                TournamentFormat::DoubleElimination => $this->doubleElimination($tournament, $participants),
                TournamentFormat::RoundRobin => $this->roundRobin($tournament, $participants),
                TournamentFormat::GroupsKnockout => $this->groupsKnockout($tournament, $participants),
                default => throw new \RuntimeException("Format {$format->value} not yet supported by generator."),
            };
        });
    }

    /**
     * @return Collection<int, Model>
     */
    protected function resolveParticipants(Tournament $tournament): Collection
    {
        return $tournament->registrations()
            ->with('participant')
            ->get()
            ->pluck('participant')
            ->filter()
            ->shuffle()
            ->values();
    }

    protected function wipeExistingStructure(Tournament $tournament): void
    {
        $tournament->matches()->delete();
        $tournament->rounds()->delete();
        $tournament->tournamentGroups()->delete();
        $tournament->standings()->delete();
    }

    /**
     * @param  Collection<int, Model>  $participants
     */
    protected function singleElimination(Tournament $tournament, Collection $participants): Tournament
    {
        $count = $participants->count();
        $bracketSize = $this->nextPowerOfTwo($count);
        $rounds = (int) log($bracketSize, 2);
        $byes = $bracketSize - $count;

        $roundIds = [];
        for ($r = 1; $r <= $rounds; $r++) {
            $round = Round::create([
                'tournament_id' => $tournament->id,
                'group_id' => null,
                'name' => $this->roundName($tournament->format, $r, $rounds),
                'number' => $r,
                'is_knockout' => true,
            ]);
            $roundIds[$r] = $round->id;
        }

        $matchesPerRound = [];
        $matchesPerRoundCount = [];
        for ($r = 1; $r <= $rounds; $r++) {
            $matchesPerRound[$r] = [];
            $matchesPerRoundCount[$r] = (int) ($bracketSize / pow(2, $r));
        }

        for ($r = 1; $r <= $rounds; $r++) {
            for ($m = 1; $m <= $matchesPerRoundCount[$r]; $m++) {
                $game = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round_id' => $roundIds[$r],
                    'round_number' => $r,
                    'match_number' => $m,
                    'bracket_index' => $m - 1,
                    'status' => MatchStatus::Scheduled,
                ]);
                $matchesPerRound[$r][$m] = $game->id;
            }
        }

        for ($r = 1; $r <= $rounds; $r++) {
            for ($m = 1; $m <= $matchesPerRoundCount[$r]; $m++) {
                $nextRound = $r + 1;
                if (! isset($matchesPerRound[$nextRound])) {
                    continue;
                }
                $targetIndex = (int) ceil($m / 2);
                GameMatch::where('id', $matchesPerRound[$r][$m])
                    ->update(['next_match_id' => $matchesPerRound[$nextRound][$targetIndex]]);
            }
        }

        $seeded = $this->seedBracket($participants, $bracketSize, $byes);

        $r1Matches = GameMatch::where('tournament_id', $tournament->id)
            ->where('round_number', 1)
            ->orderBy('match_number')
            ->get();

        foreach ($r1Matches as $i => $match) {
            $a = $seeded[$i * 2] ?? null;
            $b = $seeded[$i * 2 + 1] ?? null;
            $this->attachParticipant($match, $a, 'home');
            $this->attachParticipant($match, $b, 'away');
        }

        $this->seedStandings($tournament, $participants);

        return $tournament;
    }

    /**
     * @param  Collection<int, Model>  $participants
     */
    protected function doubleElimination(Tournament $tournament, Collection $participants): Tournament
    {
        $count = $participants->count();
        $bracketSize = $this->nextPowerOfTwo($count);
        $rounds = (int) log($bracketSize, 2);
        $byes = $bracketSize - $count;

        $winnersRoundIds = [];
        for ($r = 1; $r <= $rounds; $r++) {
            $winnersRoundIds[$r] = Round::create([
                'tournament_id' => $tournament->id,
                'group_id' => null,
                'name' => $this->roundName(TournamentFormat::SingleElimination, $r, $rounds),
                'number' => $r,
                'is_knockout' => true,
            ])->id;
        }
        $losersRounds = $rounds * 2;
        $losersRoundIds = [];
        for ($r = 1; $r <= $losersRounds; $r++) {
            $losersRoundIds[$r] = Round::create([
                'tournament_id' => $tournament->id,
                'group_id' => null,
                'name' => 'Losers R'.$r,
                'number' => $r + $rounds,
                'is_knockout' => true,
            ])->id;
        }
        $grandFinalRound = Round::create([
            'tournament_id' => $tournament->id,
            'group_id' => null,
            'name' => 'Grand Final',
            'number' => $rounds + $losersRounds + 1,
            'is_knockout' => true,
        ])->id;

        $winnersMatches = [];
        for ($r = 1; $r <= $rounds; $r++) {
            $winnersMatches[$r] = [];
            $countInRound = (int) ($bracketSize / pow(2, $r));
            for ($m = 1; $m <= $countInRound; $m++) {
                $winnersMatches[$r][$m] = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round_id' => $winnersRoundIds[$r],
                    'round_number' => $r,
                    'match_number' => $m,
                    'bracket_side' => 'winners',
                    'bracket_index' => $m - 1,
                    'status' => MatchStatus::Scheduled,
                ])->id;
            }
        }

        for ($r = 1; $r <= $rounds; $r++) {
            $next = $r + 1;
            if (! isset($winnersMatches[$next])) {
                continue;
            }
            foreach ($winnersMatches[$r] as $m => $id) {
                $targetIndex = (int) ceil($m / 2);
                GameMatch::where('id', $id)->update(['next_match_id' => $winnersMatches[$next][$targetIndex]]);
            }
        }

        $losersMatches = [];
        $losersMatches[1] = [];
        foreach ($winnersMatches[1] as $m => $id) {
            $losersMatches[1][$m] = GameMatch::create([
                'tournament_id' => $tournament->id,
                'round_id' => $losersRoundIds[1],
                'round_number' => 1,
                'match_number' => $m,
                'bracket_side' => 'losers',
                'bracket_index' => $m - 1,
                'status' => MatchStatus::Scheduled,
                'notes' => 'Loser of Winners R1',
            ])->id;
        }

        for ($lr = 2; $lr <= $losersRounds; $lr++) {
            $losersMatches[$lr] = [];
            $previousSide = ($lr % 2 === 0) ? 'cross' : 'pair';
            $previous = $losersMatches[$lr - 1];
            $prevCount = count($previous);
            $countInRound = (int) ceil($prevCount / 2);
            for ($m = 1; $m <= $countInRound; $m++) {
                $losersMatches[$lr][$m] = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round_id' => $losersRoundIds[$lr],
                    'round_number' => $lr,
                    'match_number' => $m,
                    'bracket_side' => 'losers',
                    'bracket_index' => $m - 1,
                    'status' => MatchStatus::Scheduled,
                ])->id;
            }
        }

        $winnersFinal = $winnersMatches[$rounds][1] ?? null;
        $losersFinal = $losersMatches[$losersRounds][1] ?? null;

        $grandFinalId = GameMatch::create([
            'tournament_id' => $tournament->id,
            'round_id' => $grandFinalRound,
            'round_number' => $rounds + $losersRounds + 1,
            'match_number' => 1,
            'bracket_side' => 'grand',
            'bracket_index' => 0,
            'status' => MatchStatus::Scheduled,
            'notes' => 'Winners champion vs Losers champion',
        ])->id;

        if ($winnersFinal) {
            GameMatch::where('id', $winnersFinal)->update(['next_match_id' => $grandFinalId, 'notes' => 'Winners bracket final']);
        }
        if ($losersFinal) {
            GameMatch::where('id', $losersFinal)->update(['next_match_id' => $grandFinalId, 'notes' => 'Losers bracket final']);
        }

        $seeded = $this->seedBracket($participants, $bracketSize, $byes);

        $r1 = GameMatch::where('tournament_id', $tournament->id)
            ->where('bracket_side', 'winners')
            ->where('round_number', 1)
            ->orderBy('match_number')
            ->get();

        foreach ($r1 as $i => $match) {
            $this->attachParticipant($match, $seeded[$i * 2] ?? null, 'home');
            $this->attachParticipant($match, $seeded[$i * 2 + 1] ?? null, 'away');
        }

        $this->seedStandings($tournament, $participants);

        return $tournament;
    }

    /**
     * @param  Collection<int, Model>  $participants
     */
    protected function roundRobin(Tournament $tournament, Collection $participants): Tournament
    {
        $count = $participants->count();
        $roundsCount = $count % 2 === 0 ? $count - 1 : $count;
        $participantsList = $participants->values()->all();

        $hasBye = $count % 2 === 1;
        if ($hasBye) {
            $participantsList[] = null;
            $count++;
        }

        $roundIds = [];
        for ($r = 1; $r <= $roundsCount; $r++) {
            $roundIds[$r] = Round::create([
                'tournament_id' => $tournament->id,
                'group_id' => null,
                'name' => "Round {$r}",
                'number' => $r,
                'is_knockout' => false,
            ])->id;
        }

        $rotation = $participantsList;
        for ($r = 1; $r <= $roundsCount; $r++) {
            $pairCount = (int) ($count / 2);
            for ($m = 0; $m < $pairCount; $m++) {
                $home = $rotation[$m];
                $away = $rotation[$count - 1 - $m];
                if ($home === null || $away === null) {
                    continue;
                }
                $game = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round_id' => $roundIds[$r],
                    'round_number' => $r,
                    'match_number' => $m + 1,
                    'status' => MatchStatus::Scheduled,
                ]);
                $this->attachParticipant($game, $home, 'home');
                $this->attachParticipant($game, $away, 'away');
            }

            $fixed = array_shift($rotation);
            $rest = $rotation;
            $last = array_pop($rest);
            array_unshift($rest, $last);
            array_unshift($rest, $fixed);
            $rotation = $rest;
        }

        $this->seedStandings($tournament, $participants);

        return $tournament;
    }

    /**
     * @param  Collection<int, Model>  $participants
     */
    protected function groupsKnockout(Tournament $tournament, Collection $participants): Tournament
    {
        $count = $participants->count();
        $groupCount = max(2, (int) ceil(sqrt($count)));
        $groupCount = min($groupCount, $count);

        $groupIds = [];
        for ($g = 0; $g < $groupCount; $g++) {
            $groupIds[] = TournamentGroup::create([
                'tournament_id' => $tournament->id,
                'name' => 'Group '.chr(65 + $g),
                'code' => chr(65 + $g),
                'display_order' => $g + 1,
            ])->id;
        }

        $shuffled = $participants->shuffle()->values();
        $grouped = $shuffled->chunk((int) ceil($shuffled->count() / $groupCount));

        $round1 = Round::create([
            'tournament_id' => $tournament->id,
            'name' => 'Group stage',
            'number' => 1,
            'is_knockout' => false,
        ]);

        $groupIndex = 0;
        foreach ($grouped as $gi => $groupParticipants) {
            $groupId = $groupIds[$gi];
            $groupList = $groupParticipants->values()->all();

            $n = count($groupList);
            $roundsNeeded = $n % 2 === 0 ? $n - 1 : $n;
            $hasBye = $n % 2 === 1;
            $workingList = $hasBye ? array_merge($groupList, [null]) : $groupList;

            $rotation = $workingList;
            for ($r = 1; $r <= $roundsNeeded; $r++) {
                $pairCount = (int) (count($workingList) / 2);
                for ($m = 0; $m < $pairCount; $m++) {
                    $home = $rotation[$m];
                    $away = $rotation[count($workingList) - 1 - $m];
                    if ($home === null || $away === null) {
                        continue;
                    }
                    $game = GameMatch::create([
                        'tournament_id' => $tournament->id,
                        'round_id' => $round1->id,
                        'round_number' => $r,
                        'match_number' => $m + 1,
                        'group_id' => $groupId,
                        'status' => MatchStatus::Scheduled,
                    ]);
                    $this->attachParticipant($game, $home, 'home');
                    $this->attachParticipant($game, $away, 'away');
                }
                $fixed = array_shift($rotation);
                $rest = $rotation;
                $last = array_pop($rest);
                array_unshift($rest, $last);
                array_unshift($rest, $fixed);
                $rotation = $rest;
            }
            $groupIndex++;
        }

        $this->seedStandings($tournament, $participants);

        return $tournament;
    }

    /**
     * @param  Collection<int, Model>|array<int, Model|null>  $participants
     * @return array<int, Model|null>
     */
    protected function seedBracket(iterable $participants, int $bracketSize, int $byes): array
    {
        $list = is_array($participants) ? array_values($participants) : $participants->values()->all();
        $seeded = array_pad($list, $bracketSize, null);
        $seeded = array_slice($seeded, 0, $bracketSize);

        $pairs = (int) ($bracketSize / 2);
        $result = [];
        for ($i = 0; $i < $pairs; $i++) {
            $result[] = $seeded[$i] ?? null;
            $result[] = $seeded[$bracketSize - 1 - $i] ?? null;
        }

        return $result;
    }

    protected function attachParticipant(GameMatch $match, ?Model $participant, string $side): void
    {
        if ($participant === null) {
            return;
        }
        MatchParticipant::create([
            'match_id' => $match->id,
            'participant_id' => $participant->id,
            'participant_type' => $participant->getMorphClass(),
            'side' => $side,
            'score' => 0,
            'is_winner' => false,
        ]);
    }

    /**
     * @param  Collection<int, Model>  $participants
     */
    protected function seedStandings(Tournament $tournament, Collection $participants): void
    {
        foreach ($participants as $participant) {
            Standing::create([
                'tournament_id' => $tournament->id,
                'group_id' => null,
                'participant_id' => $participant->id,
                'participant_type' => $participant->getMorphClass(),
            ]);
        }
    }

    protected function nextPowerOfTwo(int $n): int
    {
        $p = 1;
        while ($p < $n) {
            $p *= 2;
        }

        return $p;
    }

    protected function roundName(TournamentFormat $format, int $r, int $total): string
    {
        $remaining = $total - $r + 1;

        return match ($remaining) {
            1 => 'Final',
            2 => 'Semifinal',
            3 => 'Cuartos de final',
            4 => 'Octavos de final',
            default => "Round {$r}",
        };
    }
}
