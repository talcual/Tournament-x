<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tournament::with('sport')
            ->whereIn('status', ['registration', 'seeding', 'in_progress', 'completed'])
            ->orderByDesc('is_featured')
            ->orderBy('starts_at');

        if ($sport = $request->get('sport')) {
            $query->whereHas('sport', fn ($q) => $q->where('slug', $sport));
        }
        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $tournaments = $query->paginate(12)->withQueryString();

        $sports = Sport::orderBy('name')->get();

        return view('public.index', compact('tournaments', 'sports'));
    }

    public function show(Tournament $tournament): View
    {
        $tournament->load(['sport', 'organizer', 'venues', 'tournamentGroups']);

        $participants = $tournament->registrations()->with('participant')->get();

        $matches = $tournament->matches()
            ->with(['participants.participant', 'venue'])
            ->orderBy('round_number')
            ->orderBy('bracket_index')
            ->orderBy('match_number')
            ->get();

        $standings = $tournament->standings()
            ->with('participant')
            ->orderByDesc('points')
            ->orderByRaw('(goals_for - goals_against) DESC')
            ->orderByDesc('wins')
            ->get();

        $position = 1;
        foreach ($standings as $row) {
            $row->position = $position++;
        }

        return view('public.show', [
            'tournament' => $tournament,
            'participants' => $participants,
            'matches' => $matches,
            'standings' => $standings,
        ]);
    }
}
