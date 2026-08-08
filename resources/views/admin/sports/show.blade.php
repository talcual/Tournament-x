@extends('layouts.admin', ['title' => $sport->name])

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl">
        <div class="flex items-start gap-4">
            <div class="text-4xl">{{ $sport->icon ?? '🎯' }}</div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900">{{ $sport->name }}</h2>
                <p class="text-sm text-gray-500">{{ $sport->slug }}</p>
                @if($sport->description)<p class="mt-3 text-sm text-gray-700">{{ $sport->description }}</p>@endif
            </div>
        </div>

        <dl class="grid grid-cols-3 gap-4 mt-6 text-center">
            <div class="p-4 bg-gray-50 rounded-md">
                <dt class="text-xs text-gray-500 uppercase">Tournaments</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $sport->tournaments_count }}</dd>
            </div>
            <div class="p-4 bg-gray-50 rounded-md">
                <dt class="text-xs text-gray-500 uppercase">Teams</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $sport->teams_count }}</dd>
            </div>
            <div class="p-4 bg-gray-50 rounded-md">
                <dt class="text-xs text-gray-500 uppercase">Players</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $sport->players_count }}</dd>
            </div>
        </dl>
    </div>
@endsection