@extends('layouts.public', ['title' => __('app.public.discover_title')])

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('app.public.discover_title') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('app.public.discover_subtitle') }}</p>
    </div>

    <form method="GET" class="bg-white shadow rounded-lg p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('app.public.search_placeholder') }}" class="rounded-md border-gray-300 md:col-span-2 sm:text-sm">
        <select name="sport" class="rounded-md border-gray-300 sm:text-sm">
            <option value="">{{ __('app.public.all_sports') }}</option>
            @foreach($sports as $sport)
                <option value="{{ $sport->slug }}" @selected(request('sport') === $sport->slug)>{{ $sport->icon }} {{ $sport->name }}</option>
            @endforeach
        </select>
        <button class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">{{ __('app.public.filter') }}</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($tournaments as $tournament)
            <a href="{{ route('public.tournaments.show', $tournament) }}" class="block bg-white rounded-lg shadow hover:shadow-md transition p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-indigo-600">{{ $tournament->sport->name ?? '—' }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-gray-900">{{ $tournament->name }}</h3>
                    </div>
                    @if($tournament->is_featured)<span class="text-yellow-500" title="{{ __('app.public.featured') }}">★</span>@endif
                </div>

                <p class="mt-3 text-sm text-gray-600 line-clamp-3">{{ $tournament->description }}</p>

                <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $tournament->format->label() }}</span>
                    <span>{{ $tournament->starts_at?->format('M d, Y') ?? __('app.public.tbd') }}</span>
                </div>

                <div class="mt-3">
                    <x-badge :color="$tournament->status->color()">{{ $tournament->status->label() }}</x-badge>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow p-8 text-center text-gray-500">
                {{ __('app.public.no_tournaments') }}
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $tournaments->links() }}</div>
@endsection