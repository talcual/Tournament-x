@extends('layouts.admin', ['title' => __('app.admin.matches.title').' · '.$tournament->name])

@section('content')
    <a href="{{ route('admin.tournaments.show', $tournament) }}" class="text-sm text-indigo-600 hover:text-indigo-800">← {{ $tournament->name }}</a>

    <div class="mt-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('app.admin.matches.title') }} — {{ $tournament->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.tournaments.standings', $tournament) }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm">{{ __('app.admin.standings.title') }}</a>
            <a href="{{ route('admin.tournaments.draw', $tournament) }}" class="px-3 py-2 bg-yellow-500 text-white rounded-md text-sm">{{ __('app.admin.draw.title') }}</a>
        </div>
    </div>

    @if($tournament->matches->isEmpty())
        <p class="mt-6 text-sm text-gray-500">{{ __('app.admin.matches.no_matches') }}</p>
    @else
        @foreach($matchesByRound as $side => $matches)
            <div class="mt-6">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                    @if($side === 'winners')
                        Winners bracket
                    @elseif($side === 'losers')
                        Losers bracket
                    @elseif($side === 'grand')
                        Grand Final
                    @else
                        Main
                    @endif
                </h2>
                <ul class="mt-2 space-y-2">
                    @php
                        $groupedByRound = $matches->groupBy('round_number');
                    @endphp
                    @foreach($groupedByRound as $roundNumber => $roundMatches)
                        <li class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-4 py-2 border-b border-gray-200 text-xs text-gray-500 uppercase">
                                @if($side === 'main' || $side === 'winners' || $side === 'losers')
                                    {{ __('app.admin.matches.round', ['number' => $roundNumber]) }}
                                @else
                                    {{ __('app.matches.final') ?? 'Final' }}
                                @endif
                            </div>
                            <ul class="divide-y divide-gray-100">
                                @foreach($roundMatches as $match)
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
                                                    <span class="text-gray-400">TBD</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                @if($match->scheduled_at)
                                                    {{ $match->scheduled_at->format('M d, H:i') }}
                                                @endif
                                                @if($match->venue)
                                                    · {{ $match->venue->name }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <x-badge :color="match_status_color($match->status)">{{ $match->status->label() }}</x-badge>
                                            @if($match->status === \App\Enums\MatchStatus::Finished && $match->participants->count() >= 2)
                                                <p class="mt-1 text-sm text-gray-700">
                                                    {{ $match->participants[0]->score }} – {{ $match->participants[1]->score }}
                                                </p>
                                            @endif
                                            @if($match->status !== \App\Enums\MatchStatus::Finished && $mp->count() >= 2)
                                                <a href="{{ route('admin.tournaments.matches.finish', ['tournament' => $tournament, 'match' => $match]) }}" class="mt-1 inline-block text-xs text-indigo-600 hover:text-indigo-800">
                                                    {{ __('app.admin.matches.finish') }} →
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @endif
@endsection