@extends('layouts.public', ['title' => $tournament->name])

@section('content')
    <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← {{ __('app.nav.tournaments') }}</a>

    <div class="mt-4 bg-white shadow rounded-lg p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-wide text-indigo-600">{{ $tournament->sport->name ?? '—' }}</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $tournament->name }}</h1>
                <p class="mt-2 text-sm text-gray-500">
                    {{ $tournament->format->label() }} · {{ __('app.public.organized_by') }} {{ $tournament->organizer->name ?? '—' }}
                </p>
            </div>
            <x-badge :color="$tournament->status->color()">{{ $tournament->status->label() }}</x-badge>
        </div>

        @if($tournament->description)
            <p class="mt-5 text-gray-700 whitespace-pre-line">{{ $tournament->description }}</p>
        @endif

        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.starts') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->starts_at?->format('M d, Y') ?? __('app.public.tbd') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.ends') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->ends_at?->format('M d, Y') ?? __('app.public.tbd') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.registration_deadline') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->registration_deadline?->format('M d, Y') ?? __('app.public.open') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 uppercase">{{ __('app.public.slots') }}</dt>
                <dd class="text-sm text-gray-900">{{ $tournament->min_participants }}–{{ $tournament->max_participants ?? '∞' }}</dd>
            </div>
        </dl>

        @if($tournament->venues->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">{{ __('app.public.venues') }}</h3>
                <ul class="mt-2 text-sm text-gray-700 space-y-1">
                    @foreach($tournament->venues as $venue)
                        <li>{{ $venue->name }} — {{ $venue->city }}, {{ $venue->country }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('app.admin.matches.title') }} ({{ $matches->count() }})</h3>

            @if($matches->isEmpty())
                <p class="mt-3 text-sm text-gray-500">{{ __('app.public.no_matches') }}</p>
            @else
                <ul class="mt-4 divide-y divide-gray-200 border rounded-md">
                    @foreach($matches as $match)
                        <li class="px-4 py-3 flex items-center justify-between gap-3">
                            <div class="flex-1">
                                @php $mp = $match->participants; @endphp
                                <p class="text-sm font-medium text-gray-900">
                                    @if($mp->count() >= 2)
                                        {{ $mp[0]->participant->name ?? $mp[0]->participant->full_name ?? '—' }}
                                        <span class="text-gray-400 mx-1">vs</span>
                                        {{ $mp[1]->participant->name ?? $mp[1]->participant->full_name ?? '—' }}
                                    @elseif($mp->count() === 1)
                                        {{ $mp[0]->participant->name ?? $mp[0]->participant->full_name ?? '—' }}
                                        <span class="text-gray-400 mx-1">vs</span>
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <span class="text-gray-400">{{ __('app.public.tbd') }}</span>
                                    @endif
                                </p>
                                @if($match->scheduled_at)
                                    <p class="text-xs text-gray-500">{{ $match->scheduled_at->format('M d, H:i') }} · {{ $match->venue?->name ?? __('app.public.tbd') }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <x-badge :color="match_status_color($match->status)">{{ $match->status->label() }}</x-badge>
                                @if($match->status === \App\Enums\MatchStatus::Finished && $match->participants->count() >= 2)
                                    <p class="mt-1 text-xs text-gray-700">
                                        {{ $match->participants[0]->score }} – {{ $match->participants[1]->score }}
                                    </p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('app.public.standings') }}</h3>

            @if($standings->isEmpty())
                <p class="mt-3 text-sm text-gray-500">{{ __('app.public.no_standings') }}</p>
            @else
                <table class="mt-4 w-full text-sm">
                    <thead class="text-xs text-gray-500 uppercase border-b">
                        <tr>
                            <th class="text-left py-1">{{ __('app.admin.standings.pos') }}</th>
                            <th class="text-left py-1">{{ __('app.admin.standings.participant') }}</th>
                            <th class="text-right py-1">{{ __('app.admin.standings.points') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($standings as $row)
                            <tr>
                                <td class="py-1">{{ $row->position ?? '—' }}</td>
                                <td class="py-1">{{ $row->participant->name ?? $row->participant->full_name ?? '—' }}</td>
                                <td class="py-1 text-right font-medium">{{ $row->points }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900">{{ __('app.public.registered_participants', ['count' => $participants->count()]) }}</h3>

        @if($participants->isEmpty())
            <p class="mt-3 text-sm text-gray-500">{{ __('app.public.no_participants') }}</p>
        @else
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($participants as $reg)
                    <div class="border rounded-md p-3 flex items-center gap-3">
                        <div class="h-9 w-9 bg-gray-100 rounded-full flex items-center justify-center text-sm">
                            @if($reg->participant_type === 'team')🏆@else⭐@endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $reg->participant->name ?? $reg->participant->full_name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ class_basename($reg->participant_type) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection