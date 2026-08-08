<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Enums\TournamentStatus;
use App\Models\GameMatch;
use App\Models\MatchParticipant;
use App\Models\Standing;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

class FinishMatch
{
    /**
     * @param  array<int, array{participant_id: int, participant_type: string, score: int}>|null  $results
     */
    public function execute(GameMatch $match, ?array $results = null, ?int $winnerParticipantId = null, bool $isDraw = false): GameMatch
    {
        return DB::transaction(function () use ($match, $results, $winnerParticipantId) {
            $match->load(['tournament.sport', 'participants.participant']);

            if ($match->status === MatchStatus::Finished) {
                throw new \RuntimeException('Match already finished.');
            }

            $sport = $match->tournament->sport;

            if ($results === null) {
                $results = $match->participants->map(fn (MatchParticipant $p) => [
                    'participant_id' => $p->participant_id,
                    'participant_type' => $p->participant_type,
                    'score' => 0,
                ])->values()->all();
            }

            $byParticipant = [];
            foreach ($results as $r) {
                $key = $r['participant_type'].':'.$r['participant_id'];
                $byParticipant[$key] = (int) $r['score'];
            }

            $previous = [];
            foreach ($match->participants as $participant) {
                $previous[$participant->participant_type.':'.$participant->participant_id] = [
                    'wins' => 0, 'draws' => 0, 'losses' => 0, 'gf' => 0, 'ga' => 0,
                ];
            }

            foreach ($match->participants as $participant) {
                $key = $participant->participant_type.':'.$participant->participant_id;
                $score = $byParticipant[$key] ?? 0;
                $participant->update(['score' => $score]);
            }

            $participantIds = $match->participants->pluck('participant_id', 'participant_type.'.':id'.':');
            $keys = array_keys($byParticipant);
            $orderedKeys = $keys;

            $scoreA = $byParticipant[$orderedKeys[0] ?? ''] ?? 0;
            $scoreB = $byParticipant[$orderedKeys[1] ?? ''] ?? 0;

            $isActualDraw = $scoreA === $scoreB;

            foreach ($match->participants as $i => $participant) {
                $key = $orderedKeys[$i] ?? '';
                $oppKey = $orderedKeys[1 - $i] ?? '';
                $ownScore = $byParticipant[$key] ?? 0;
                $oppScore = $byParticipant[$oppKey] ?? 0;
                $isWinner = false;
                $isDrawMatch = $isActualDraw;

                if (! $isDrawMatch && $winnerParticipantId !== null) {
                    $isWinner = $winnerParticipantId === $participant->participant_id
                        && $participant->participant_type === ($results[0]['participant_type'] ?? $participant->participant_type);
                } elseif (! $isDrawMatch) {
                    $isWinner = $ownScore > $oppScore;
                }

                $participant->update(['is_winner' => $isWinner]);

                if ($isDrawMatch) {
                    if (! $sport->allows_draws && $participant->id === ($match->participants->first()->id ?? null)) {
                        throw new \RuntimeException("Sport {$sport->name} does not allow draws.");
                    }
                    $previous[$key]['draws'] = 1;
                } else {
                    $previous[$key][$isWinner ? 'wins' : 'losses'] = 1;
                }
                $previous[$key]['gf'] = $ownScore;
                $previous[$key]['ga'] = $oppScore;
            }

            foreach ($previous as $key => $delta) {
                [$type, $id] = explode(':', $key);
                $standing = Standing::where('tournament_id', $match->tournament_id)
                    ->where('group_id', $match->group_id)
                    ->where('participant_id', $id)
                    ->where('participant_type', $type)
                    ->first();
                if (! $standing) {
                    $standing = Standing::create([
                        'tournament_id' => $match->tournament_id,
                        'group_id' => $match->group_id,
                        'participant_id' => $id,
                        'participant_type' => $type,
                    ]);
                }
                $standing->increment('played');
                $standing->increment('wins', $delta['wins']);
                $standing->increment('draws', $delta['draws']);
                $standing->increment('losses', $delta['losses']);
                $standing->increment('goals_for', $delta['gf']);
                $standing->increment('goals_against', $delta['ga']);

                $winPts = $delta['wins'] * $sport->points_per_win;
                $drawPts = $delta['draws'] * $sport->points_per_draw;
                $lossPts = $delta['losses'] * $sport->points_per_loss;
                $standing->increment('points', $winPts + $drawPts + $lossPts);
            }

            $match->update(['status' => MatchStatus::Finished]);

            $this->propagateWinner($match);

            return $match->refresh();
        });
    }

    protected function propagateWinner(GameMatch $match): void
    {
        if (! $match->next_match_id) {
            $this->markTournamentCompletedIfFinal($match);

            return;
        }

        $winner = $match->participants->firstWhere('is_winner', true);
        if (! $winner) {
            return;
        }

        $next = GameMatch::find($match->next_match_id);
        if (! $next) {
            return;
        }

        $already = $next->participants()->where('participant_id', $winner->participant_id)
            ->where('participant_type', $winner->participant_type)
            ->exists();
        if ($already) {
            return;
        }

        $existingSides = $next->participants->pluck('side')->all();
        $side = in_array('home', $existingSides, true) ? 'away' : 'home';

        MatchParticipant::create([
            'match_id' => $next->id,
            'participant_id' => $winner->participant_id,
            'participant_type' => $winner->participant_type,
            'side' => $side,
            'score' => 0,
            'is_winner' => false,
        ]);
    }

    protected function markTournamentCompletedIfFinal(GameMatch $match): void
    {
        $tournament = Tournament::find($match->tournament_id);
        if (! $tournament) {
            return;
        }

        $allFinished = $tournament->matches()
            ->where('id', '!=', $match->id)
            ->where('status', '!=', MatchStatus::Finished->value)
            ->where('status', '!=', MatchStatus::Cancelled->value)
            ->doesntExist();

        if ($allFinished) {
            $tournament->update(['status' => TournamentStatus::Completed]);
        }
    }
}
