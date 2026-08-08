<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\Tournament;
use App\Services\FinishMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchResultController extends Controller
{
    public function edit(Tournament $tournament, GameMatch $match): View
    {
        $this->authorize('update', $tournament);
        $match->load(['participants.participant']);

        return view('admin.matches.finish', compact('tournament', 'match'));
    }

    public function update(Request $request, Tournament $tournament, GameMatch $match, FinishMatch $service): RedirectResponse
    {
        $this->authorize('update', $tournament);

        $data = $request->validate([
            'is_draw' => ['nullable', 'boolean'],
            'scores' => ['required', 'array'],
            'scores.*' => ['nullable', 'integer', 'min:0', 'max:255'],
            'winner_participant_key' => ['nullable', 'string'],
        ]);

        $isDraw = (bool) ($data['is_draw'] ?? false);

        $results = [];
        $winnerParticipantId = null;
        $winnerParticipantType = null;

        foreach ($match->participants as $i => $participant) {
            $key = $participant->participant_type.':'.$participant->participant_id;
            $score = (int) ($data['scores'][$key] ?? 0);
            $results[] = [
                'participant_id' => $participant->participant_id,
                'participant_type' => $participant->participant_type,
                'score' => $score,
            ];
            if (! $isDraw && $data['winner_participant_key'] === $key) {
                $winnerParticipantId = $participant->participant_id;
                $winnerParticipantType = $participant->participant_type;
            }
        }

        if (! $isDraw && $winnerParticipantId === null) {
            return back()->withErrors(['result' => __('app.admin.matches.winner_required')]);
        }

        try {
            $service->execute($match, $results, $winnerParticipantId, $isDraw);
        } catch (\Throwable $e) {
            return back()->withErrors(['result' => $e->getMessage()]);
        }

        return redirect()->route('admin.tournaments.matches', $tournament)
            ->with('status', __('app.admin.matches.finished_success'));
    }
}
