<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlayerRequest;
use App\Http\Requests\Admin\UpdatePlayerRequest;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Player::class);

        $query = Player::with(['sport', 'team']);

        if ($sport = $request->get('sport_id')) {
            $query->where('sport_id', $sport);
        }
        if ($team = $request->get('team_id')) {
            $query->where('team_id', $team);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $players = $query->orderBy('last_name')->paginate(15)->withQueryString();

        return view('admin.players.index', [
            'players' => $players,
            'sports' => Sport::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Player::class);

        return view('admin.players.create', [
            'player' => new Player,
            'sports' => Sport::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }

    public function store(StorePlayerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug']) && ! empty($data['first_name']) && ! empty($data['last_name'])) {
            $data['slug'] = Str::slug("{$data['first_name']}-{$data['last_name']}");
        }
        $player = Player::create($data);

        return redirect()->route('admin.players.index')->with('status', "Player '{$player->full_name}' created.");
    }

    public function show(Player $player): View
    {
        $this->authorize('view', $player);
        $player->load(['sport', 'team']);

        return view('admin.players.show', compact('player'));
    }

    public function edit(Player $player): View
    {
        $this->authorize('update', $player);

        return view('admin.players.edit', [
            'player' => $player,
            'sports' => Sport::orderBy('name')->get(),
            'teams' => Team::orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePlayerRequest $request, Player $player): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug']) && ! empty($data['first_name']) && ! empty($data['last_name'])) {
            $data['slug'] = Str::slug("{$data['first_name']}-{$data['last_name']}");
        }
        $player->update($data);

        return redirect()->route('admin.players.index')->with('status', "Player '{$player->full_name}' updated.");
    }

    public function destroy(Player $player): RedirectResponse
    {
        $this->authorize('delete', $player);
        $name = $player->full_name;
        $player->delete();

        return redirect()->route('admin.players.index')->with('status', "Player '{$name}' deleted.");
    }
}
