@extends('layouts.admin', ['title' => $player->full_name])

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl">
        <div class="flex items-start gap-4">
            <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center text-2xl">⭐</div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900">{{ $player->full_name }}</h2>
                <p class="text-sm text-gray-500">{{ $player->sport->name ?? '—' }} · {{ $player->team->name ?? 'Independent' }}</p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $player->nationality ?? '—' }} ·
                    @if($player->birth_date)Born {{ $player->birth_date->format('M d, Y') }}@endif
                </p>
            </div>
        </div>

        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 text-center">
            <div class="p-4 bg-gray-50 rounded-md">
                <dt class="text-xs text-gray-500 uppercase">Ranking</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $player->ranking ? "#{$player->ranking}" : '—' }}</dd>
            </div>
            <div class="p-4 bg-gray-50 rounded-md">
                <dt class="text-xs text-gray-500 uppercase">Rating</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $player->rating ?? '—' }}</dd>
            </div>
        </dl>
    </div>
@endsection