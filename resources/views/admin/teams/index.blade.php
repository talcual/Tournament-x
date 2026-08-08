@extends('layouts.admin', ['title' => 'Teams'])

@section('content')
    <div class="flex flex-col gap-3 mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Teams</h2>
            @can('create', App\Models\Team::class)
                <a href="{{ route('admin.teams.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">+ New Team</a>
            @endcan
        </div>
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name..." class="rounded-md border-gray-300 sm:text-sm">
            <select name="sport_id" class="rounded-md border-gray-300 sm:text-sm">
                <option value="">All sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" @selected(request('sport_id') == $sport->id)>{{ $sport->name }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm">Filter</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Team</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sport</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Players</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($teams as $team)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $team->name }}</div>
                            <div class="text-xs text-gray-500">{{ $team->coach_name ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $team->sport->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $team->players_count }}</td>
                        <td class="px-6 py-4">
                            <x-badge :color="$team->is_active ? 'green' : 'gray'">{{ $team->is_active ? 'Active' : 'Inactive' }}</x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('admin.teams.show', $team) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                            <a href="{{ route('admin.teams.edit', $team) }}" class="text-gray-700 hover:text-gray-900">Edit</a>
                            <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" class="inline" onsubmit="return confirm('Delete this team?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No teams found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $teams->links() }}</div>
@endsection