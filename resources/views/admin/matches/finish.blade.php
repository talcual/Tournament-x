@extends('layouts.admin', ['title' => __('app.admin.matches.finish')])

@section('content')
    <a href="{{ route('admin.tournaments.matches', $tournament) }}" class="text-sm text-indigo-600 hover:text-indigo-800">← {{ __('app.admin.matches.title') }}</a>

    <div class="mt-4 max-w-2xl bg-white shadow rounded-lg p-6">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('app.admin.matches.finish') }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $tournament->name }}</p>

        <form method="POST" action="{{ route('admin.tournaments.matches.finish.store', ['tournament' => $tournament, 'match' => $match]) }}" class="mt-5 space-y-4">
            @csrf

            <div class="space-y-3">
                @foreach($match->participants as $i => $participant)
                    @php $key = $participant->participant_type.':'.$participant->participant_id; @endphp
                    <div class="border rounded-md p-4 flex items-center gap-3">
                        <input type="radio" name="winner_participant_key" value="{{ $key }}" id="winner-{{ $key }}" @checked(old('winner_participant_key') === $key) class="h-4 w-4 text-indigo-600 border-gray-300">
                        <label for="winner-{{ $key }}" class="flex-1 font-medium text-gray-900">
                            {{ $participant->participant->name ?? $participant->participant->full_name ?? '—' }}
                        </label>
                        <input
                            type="number"
                            name="scores[{{ $key }}]"
                            value="{{ old('scores.'.$key, $participant->score) }}"
                            min="0"
                            max="255"
                            class="w-24 rounded-md border-gray-300 text-right"
                            placeholder="0"
                        />
                    </div>
                @endforeach
            </div>

            @if($tournament->sport->allows_draws)
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="is_draw" value="0">
                    <input type="checkbox" name="is_draw" value="1" @checked(old('is_draw')) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    {{ __('app.admin.matches.draw') }}
                </label>
            @endif

            @error('result')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3 border-t pt-4">
                <a href="{{ route('admin.tournaments.matches', $tournament) }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">{{ __('app.admin.actions.cancel') }}</a>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">{{ __('app.admin.matches.save_result') }}</button>
            </div>
        </form>
    </div>
@endsection