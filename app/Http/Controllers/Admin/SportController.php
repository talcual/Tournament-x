<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSportRequest;
use App\Http\Requests\Admin\UpdateSportRequest;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sport::class);

        $query = Sport::query()->withCount(['tournaments', 'teams', 'players']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $sports = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.sports.index', compact('sports'));
    }

    public function create(): View
    {
        $this->authorize('create', Sport::class);

        return view('admin.sports.create', ['sport' => new Sport]);
    }

    public function store(StoreSportRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $sport = Sport::create($data);

        return redirect()->route('admin.sports.index')->with('status', "Sport '{$sport->name}' created.");
    }

    public function show(Sport $sport): View
    {
        $this->authorize('view', $sport);

        $sport->loadCount(['tournaments', 'teams', 'players']);

        return view('admin.sports.show', compact('sport'));
    }

    public function edit(Sport $sport): View
    {
        $this->authorize('update', $sport);

        return view('admin.sports.edit', compact('sport'));
    }

    public function update(UpdateSportRequest $request, Sport $sport): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $sport->update($data);

        return redirect()->route('admin.sports.index')->with('status', "Sport '{$sport->name}' updated.");
    }

    public function destroy(Sport $sport): RedirectResponse
    {
        $this->authorize('delete', $sport);
        $name = $sport->name;
        $sport->delete();

        return redirect()->route('admin.sports.index')->with('status', "Sport '{$name}' deleted.");
    }
}
