@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($stats as $stat)
            <x-stat-card :title="$stat['label']" :icon="$stat['icon']">{{ $stat['value'] }}</x-stat-card>
        @endforeach
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white shadow rounded-lg">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Upcoming Tournaments</h3>
                <a href="{{ route('admin.tournaments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all →</a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($upcomingTournaments as $tournament)
                    <a href="{{ route('admin.tournaments.show', $tournament) }}" class="block px-5 py-3 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $tournament->name }}</p>
                                <p class="text-xs text-gray-500">{{ $tournament->sport->name ?? '—' }} · {{ $tournament->starts_at?->format('M d, Y') ?? 'TBD' }}</p>
                            </div>
                            <x-badge :color="$tournament->status->color()">{{ $tournament->status->label() }}</x-badge>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-6 text-center text-sm text-gray-500">No upcoming tournaments.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-5">
            <h3 class="text-base font-semibold text-gray-900">Quick actions</h3>
            <div class="mt-4 space-y-2">
                <a href="{{ route('admin.tournaments.create') }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Create tournament</a>
                <a href="{{ route('admin.teams.create') }}" class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">New team</a>
                <a href="{{ route('admin.players.create') }}" class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">New player</a>
            </div>
        </div>
    </div>
@endsection