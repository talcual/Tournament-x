<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVenueRequest;
use App\Http\Requests\Admin\UpdateVenueRequest;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Venue::class);

        $query = Venue::query();

        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $venues = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.venues.index', compact('venues'));
    }

    public function create(): View
    {
        $this->authorize('create', Venue::class);

        return view('admin.venues.create', ['venue' => new Venue]);
    }

    public function store(StoreVenueRequest $request): RedirectResponse
    {
        $venue = Venue::create($request->validated());

        return redirect()->route('admin.venues.index')->with('status', "Venue '{$venue->name}' created.");
    }

    public function show(Venue $venue): View
    {
        $this->authorize('view', $venue);

        return view('admin.venues.show', compact('venue'));
    }

    public function edit(Venue $venue): View
    {
        $this->authorize('update', $venue);

        return view('admin.venues.edit', compact('venue'));
    }

    public function update(UpdateVenueRequest $request, Venue $venue): RedirectResponse
    {
        $venue->update($request->validated());

        return redirect()->route('admin.venues.index')->with('status', "Venue '{$venue->name}' updated.");
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $this->authorize('delete', $venue);
        $name = $venue->name;
        $venue->delete();

        return redirect()->route('admin.venues.index')->with('status', "Venue '{$name}' deleted.");
    }
}
