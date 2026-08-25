<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ParticipantType;
use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTournamentRequest;
use App\Http\Requests\Admin\UpdateTournamentRequest;
use App\Models\GameMatch;
use App\Models\Sport;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Venue;
use App\Services\BracketGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tournament::class);

        $query = Tournament::with(['sport', 'organizer'])
            ->withCount(['registrations', 'matches']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($sport = $request->get('sport_id')) {
            $query->where('sport_id', $sport);
        }
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $tournaments = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.tournaments.index', [
            'tournaments' => $tournaments,
            'statuses' => TournamentStatus::cases(),
            'sports' => Sport::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Tournament::class);

        return view('admin.tournaments.create', [
            'tournament' => new Tournament,
            'sports' => Sport::orderBy('name')->get(),
            'venues' => Venue::where('is_active', true)->orderBy('name')->get(),
            'organizers' => User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'organizer', 'super-admin']))->orderBy('name')->get(),
            'formats' => TournamentFormat::cases(),
            'statuses' => TournamentStatus::cases(),
            'participantTypes' => ParticipantType::cases(),
        ]);
    }

    public function userCreate(): View
    {
        $this->authorize('create', Tournament::class);

        return view('user.tournaments.create', [
            'sports' => Sport::orderBy('name')->get(),
            'formats' => TournamentFormat::cases(),
            'statuses' => TournamentStatus::cases(),
            'participantTypes' => ParticipantType::cases(),
        ]);
    }

    public function store(StoreTournamentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $venues = $data['venues'] ?? [];
        unset($data['venues']);

        if (empty($data['organizer_id'])) {
            $data['organizer_id'] = $request->user()->id;
        }

        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $tournament = Tournament::create($data);

        if ($venues) {
            $tournament->venues()->sync($venues);
        }

        return redirect()->route('admin.tournaments.index')->with('status', "Tournament '{$tournament->name}' created.");
    }

    public function userStore(StoreTournamentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['venues']);

        $data['organizer_id'] = $request->user()->id;

        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $tournament = Tournament::create($data);

        return redirect()->route('dashboard')->with('status', "Tournament '{$tournament->name}' created.");
    }

    public function show(Tournament $tournament): View
    {
        $this->authorize('view', $tournament);

        $tournament->load(['sport', 'organizer', 'venues', 'registrations.participant']);

        return view('admin.tournaments.show', compact('tournament'));
    }

    public function edit(Tournament $tournament): View
    {
        $this->authorize('update', $tournament);

        return view('admin.tournaments.edit', [
            'tournament' => $tournament,
            'sports' => Sport::orderBy('name')->get(),
            'venues' => Venue::where('is_active', true)->orderBy('name')->get(),
            'organizers' => User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'organizer', 'super-admin']))->orderBy('name')->get(),
            'formats' => TournamentFormat::cases(),
            'statuses' => TournamentStatus::cases(),
            'participantTypes' => ParticipantType::cases(),
        ]);
    }

    public function update(UpdateTournamentRequest $request, Tournament $tournament): RedirectResponse
    {
        $data = $request->validated();
        $venues = $data['venues'] ?? [];
        unset($data['venues']);

        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $tournament->update($data);
        $tournament->venues()->sync($venues);

        return redirect()->route('admin.tournaments.index')->with('status', "Tournament '{$tournament->name}' updated.");
    }

    public function destroy(Tournament $tournament): RedirectResponse
    {
        $this->authorize('delete', $tournament);
        $name = $tournament->name;
        $tournament->delete();

        return redirect()->route('admin.tournaments.index')->with('status', "Tournament '{$name}' deleted.");
    }

    public function draw(Tournament $tournament): View
    {
        $this->authorize('update', $tournament);
        $tournament->load('registrations.participant');

        return view('admin.tournaments.draw', compact('tournament'));
    }

    public function generateDraw(Request $request, Tournament $tournament, BracketGenerator $generator): RedirectResponse
    {
        $this->authorize('update', $tournament);
        try {
            $generator->generate($tournament);
            $tournament->update(['status' => TournamentStatus::InProgress]);

            return redirect()->route('admin.tournaments.matches', $tournament)
                ->with('status', __('app.admin.draw.success'));
        } catch (\Throwable $e) {
            return back()->withErrors(['draw' => $e->getMessage()]);
        }
    }

    public function matches(Tournament $tournament): View
    {
        $this->authorize('view', $tournament);

        $tournament->load([
            'rounds',
            'matches.participants.participant',
            'matches.venue',
        ]);

        $matchesByRound = $tournament->matches
            ->sortBy([['round_number', 'asc'], ['bracket_index', 'asc'], ['match_number', 'asc']])
            ->groupBy(fn (GameMatch $m) => $m->bracket_side ?? 'main');

        return view('admin.tournaments.matches', [
            'tournament' => $tournament,
            'matchesByRound' => $matchesByRound,
        ]);
    }

    public function standings(Tournament $tournament): View
    {
        $this->authorize('view', $tournament);

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

        return view('admin.tournaments.standings', compact('tournament', 'standings'));
    }
}
