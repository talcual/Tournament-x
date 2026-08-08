@extends('layouts.admin', ['title' => $tournament->name])

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-4xl">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $tournament->name }}</h2>
                    <x-badge :color="$tournament->status->color()">{{ $tournament->status->label() }}</x-badge>
                    @if($tournament->is_featured)<span class="text-yellow-500" title="Featured">★</span>@endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $tournament->sport->name ?? '—' }} · {{ $tournament->format->label() }} ·
                    {{ __('app.public.organized_by') }} {{ $tournament->organizer->name ?? '—' }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2 justify-end">
                <a href="{{ route('admin.tournaments.matches', $tournament) }}" class="px-3 py-2 bg-green-600 text-white rounded-md text-sm">{{ __('app.admin.matches.title') }}</a>
                <a href="{{ route('admin.tournaments.standings', $tournament) }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm">{{ __('app.admin.standings.title') }}</a>
                <a href="{{ route('admin.tournaments.draw', $tournament) }}" class="px-3 py-2 bg-yellow-500 text-white rounded-md text-sm">{{ __('app.admin.draw.title') }}</a>
                <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm">{{ __('app.admin.actions.edit') }}</a>
            </div>
        </div>

        @if($tournament->description)
            <p class="mt-4 text-sm text-gray-700 whitespace-pre-line">{{ $tournament->description }}</p>
        @endif

        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.starts') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->starts_at?->format('M d, Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.ends') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->ends_at?->format('M d, Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.registration_deadline') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->registration_deadline?->format('M d, Y H:i') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.slots') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->min_participants }} / {{ $tournament->max_participants ?? '∞' }}</dd>
            </div>
        </dl>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="p-3 bg-gray-50 rounded">
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.sport.points_per_win') }}</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $tournament->sport->points_per_win ?? 0 }}</dd>
            </div>
            <div class="p-3 bg-gray-50 rounded">
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.sport.points_per_draw') }}</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $tournament->sport->points_per_draw ?? 0 }}</dd>
            </div>
            <div class="p-3 bg-gray-50 rounded">
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.sport.points_per_loss') }}</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $tournament->sport->points_per_loss ?? 0 }}</dd>
            </div>
            <div class="p-3 bg-gray-50 rounded">
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.sport.allows_draws') }}</dt>
                <dd class="mt-2"><x-badge :color="$tournament->sport->allows_draws ? 'green' : 'red'">{{ $tournament->sport->allows_draws ? __('Yes') : __('No') }}</x-badge></dd>
            </div>
        </dl>

        <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('app.public.venues') }}</h3>
            @if($tournament->venues->isEmpty())
                <p class="mt-2 text-sm text-gray-500">—</p>
            @else
                <ul class="mt-2 space-y-1 text-sm text-gray-700">
                    @foreach($tournament->venues as $venue)
                        <li>
                            <span class="font-medium">{{ $venue->name }}</span>
                            @if($venue->pivot->is_primary)<span class="text-xs text-indigo-600">(primary)</span>@endif
                            — {{ $venue->city }}, {{ $venue->country }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('app.public.registered_participants', ['count' => $tournament->registrations->count()]) }}</h3>
            @if($tournament->registrations->isEmpty())
                <p class="mt-2 text-sm text-gray-500">—</p>
            @else
                <ul class="mt-2 divide-y divide-gray-200 border rounded-md">
                    @foreach($tournament->registrations as $reg)
                        <li class="px-4 py-2 flex justify-between text-sm">
                            <span>{{ $reg->participant->name ?? $reg->participant->full_name ?? '—' }}</span>
                            <span class="text-gray-500">{{ class_basename($reg->participant_type) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection