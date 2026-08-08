<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamRequest;
use App\Http\Requests\Admin\UpdateTeamRequest;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Team::class);

        $query = Team::with('sport')->withCount('players');

        if ($sport = $request->get('sport_id')) {
            $query->where('sport_id', $sport);
        }
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $teams = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.teams.index', [
            'teams' => $teams,
            'sports' => Sport::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Team::class);

        return view('admin.teams.create', [
            'team' => new Team,
            'sports' => Sport::orderBy('name')->get(),
        ]);
    }

    public function store(StoreTeamRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $team = Team::create($data);

        return redirect()->route('admin.teams.index')->with('status', "Team '{$team->name}' created.");
    }

    public function show(Team $team): View
    {
        $this->authorize('view', $team);
        $team->load(['sport', 'players']);

        return view('admin.teams.show', compact('team'));
    }

    public function edit(Team $team): View
    {
        $this->authorize('update', $team);

        return view('admin.teams.edit', [
            'team' => $team,
            'sports' => Sport::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $team->update($data);

        return redirect()->route('admin.teams.index')->with('status', "Team '{$team->name}' updated.");
    }

    public function destroy(Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);
        $name = $team->name;
        $team->delete();

        return redirect()->route('admin.teams.index')->with('status', "Team '{$name}' deleted.");
    }
}
