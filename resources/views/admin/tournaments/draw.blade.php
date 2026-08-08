@extends('layouts.admin', ['title' => __('app.admin.draw.title').' · '.$tournament->name])

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('admin.tournaments.show', $tournament) }}" class="text-sm text-indigo-600 hover:text-indigo-800">← {{ $tournament->name }}</a>

        <div class="mt-4 bg-white shadow rounded-lg p-6">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('app.admin.draw.title') }} — {{ $tournament->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $tournament->format->label() }} · {{ $tournament->sport->name ?? '—' }} · {{ $tournament->participant_type->label() }}
            </p>

            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                    {{ __('app.public.registered_participants', ['count' => $tournament->registrations->count()]) }}
                </h3>
                @if($tournament->registrations->isEmpty())
                    <p class="mt-2 text-sm text-red-600">{{ __('app.admin.draw.no_participants') }}</p>
                @else
                    <ul class="mt-2 text-sm text-gray-700 grid grid-cols-2 gap-2">
                        @foreach($tournament->registrations as $reg)
                            <li class="border rounded px-3 py-1">
                                {{ $reg->participant->name ?? $reg->participant->full_name ?? '—' }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @error('draw')
                <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <form method="POST" action="{{ route('admin.tournaments.draw.generate', $tournament) }}" class="mt-6 border-t pt-5 flex items-center justify-between"
                  onsubmit="return confirm('{{ __('app.admin.draw.confirm_regenerate') }}');">
                @csrf
                <p class="text-xs text-gray-500">
                    {{ __('app.sport.points_per_win') }}: <strong>{{ $tournament->sport->points_per_win ?? 3 }}</strong> ·
                    {{ __('app.sport.points_per_draw') }}: <strong>{{ $tournament->sport->points_per_draw ?? 1 }}</strong> ·
                    {{ __('app.sport.points_per_loss') }}: <strong>{{ $tournament->sport->points_per_loss ?? 0 }}</strong>
                </p>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700" @disabled($tournament->registrations->count() < 2)>
                    {{ $tournament->matches()->exists() ? __('app.admin.draw.regenerate') : __('app.admin.draw.generate') }}
                </button>
            </form>
        </div>
    </div>
@endsection