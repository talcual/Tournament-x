@extends('layouts.admin', ['title' => 'Players'])

@section('content')
    <div class="flex flex-col gap-3 mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Players</h2>
            @can('create', App\Models\Player::class)
                <a href="{{ route('admin.players.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">+ New Player</a>
            @endcan
        </div>
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="rounded-md border-gray-300 sm:text-sm">
            <select name="sport_id" class="rounded-md border-gray-300 sm:text-sm">
                <option value="">All sports</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" @selected(request('sport_id') == $sport->id)>{{ $sport->name }}</option>
                @endforeach
            </select>
            <select name="team_id" class="rounded-md border-gray-300 sm:text-sm">
                <option value="">All teams</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" @selected(request('team_id') == $team->id)>{{ $team->name }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm">Filter</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Player</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sport</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Team</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ranking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($players as $player)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $player->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $player->nationality ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $player->sport->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $player->team->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $player->ranking ? "#{$player->ranking}" : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $player->rating ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('admin.players.show', $player) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                            <a href="{{ route('admin.players.edit', $player) }}" class="text-gray-700 hover:text-gray-900">Edit</a>
                            <form method="POST" action="{{ route('admin.players.destroy', $player) }}" class="inline" onsubmit="return confirm('Delete this player?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No players found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $players->links() }}</div>
@endsection