@extends('layouts.admin', ['title' => 'Edit Player'])

@section('content')
    <form method="POST" action="{{ route('admin.players.update', $player) }}" class="max-w-2xl bg-white shadow rounded-lg p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input name="first_name" label="First name" :value="$player->first_name" :required="true" />
            <x-form-input name="last_name" label="Last name" :value="$player->last_name" :required="true" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-select name="sport_id" label="Sport" :options="$sports->pluck('name', 'id')->toArray()" :value="$player->sport_id" :required="true" />
            <x-form-select name="team_id" label="Team (optional)" :options=['' => '— Independent'] + $teams->pluck('name', 'id')->toArray()" :value="$player->team_id" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-input name="birth_date" label="Birth date" type="date" :value="$player->birth_date?->format('Y-m-d')" />
            <x-form-input name="nationality" label="Nationality" :value="$player->nationality" />
            <x-form-input name="slug" label="Slug" :value="$player->slug" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-input name="ranking" label="Ranking" type="number" :value="$player->ranking" />
            <x-form-input name="rating" label="Rating" type="number" :value="$player->rating" />
            <x-form-input name="photo_path" label="Photo path" :value="$player->photo_path" />
        </div>

        <x-form-checkbox name="is_active" label="Active" :value="$player->is_active" />

        <div class="flex items-center justify-end gap-3 border-t pt-5">
            <a href="{{ route('admin.players.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save changes</button>
        </div>
    </form>
@endsection