@extends('layouts.admin', ['title' => $team->name])

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl">
        <div class="flex items-start gap-4">
            <div class="h-16 w-16 bg-gray-100 rounded-md flex items-center justify-center text-2xl">🏆</div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900">{{ $team->name }}</h2>
                <p class="text-sm text-gray-500">{{ $team->sport->name ?? '—' }} · Coach: {{ $team->coach_name ?? '—' }} · Home: {{ $team->home_venue ?? '—' }}</p>
                <div class="mt-2">
                    <x-badge :color="$team->is_active ? 'green' : 'gray'">{{ $team->is_active ? 'Active' : 'Inactive' }}</x-badge>
                </div>
            </div>
        </div>

        @if($team->description)
            <p class="mt-4 text-sm text-gray-700">{{ $team->description }}</p>
        @endif

        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mt-6">Players ({{ $team->players->count() }})</h3>
        <ul class="mt-2 divide-y divide-gray-200 border rounded-md">
            @forelse($team->players as $player)
                <li class="px-4 py-2 flex justify-between text-sm">
                    <span>{{ $player->full_name }}</span>
                    <span class="text-gray-500">{{ $player->ranking ? "#{$player->ranking}" : '—' }}</span>
                </li>
            @empty
                <li class="px-4 py-2 text-sm text-gray-500">No players yet.</li>
            @endforelse
        </ul>
    </div>
@endsection