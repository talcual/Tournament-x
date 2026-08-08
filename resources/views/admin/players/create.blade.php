@extends('layouts.admin', ['title' => 'New Player'])

@section('content')
    <form method="POST" action="{{ route('admin.players.store') }}" class="max-w-2xl bg-white shadow rounded-lg p-6 space-y-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input name="first_name" label="First name" :required="true" />
            <x-form-input name="last_name" label="Last name" :required="true" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-select name="sport_id" label="Sport" :options="$sports->pluck('name', 'id')->toArray()" :required="true" />
            <x-form-select name="team_id" label="Team (optional)" :options=['' => '— Independent'] + $teams->pluck('name', 'id')->toArray()" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-input name="birth_date" label="Birth date" type="date" />
            <x-form-input name="nationality" label="Nationality" />
            <x-form-input name="slug" label="Slug" hint="Auto-generated if blank." />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-input name="ranking" label="Ranking" type="number" />
            <x-form-input name="rating" label="Rating" type="number" />
            <x-form-input name="photo_path" label="Photo path" />
        </div>

        <x-form-checkbox name="is_active" label="Active" :value="true" />

        <div class="flex items-center justify-end gap-3 border-t pt-5">
            <a href="{{ route('admin.players.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Create Player</button>
        </div>
    </form>
@endsection