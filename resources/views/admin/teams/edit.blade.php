@extends('layouts.admin', ['title' => 'Edit Team'])

@section('content')
    <form method="POST" action="{{ route('admin.teams.update', $team) }}" class="max-w-2xl bg-white shadow rounded-lg p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input name="name" label="Name" :value="$team->name" :required="true" />
            <x-form-input name="slug" label="Slug" :value="$team->slug" />
        </div>

        <x-form-select name="sport_id" label="Sport" :options="$sports->pluck('name', 'id')->toArray()" :value="$team->sport_id" :required="true" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input name="coach_name" label="Coach name" :value="$team->coach_name" />
            <x-form-input name="home_venue" label="Home venue" :value="$team->home_venue" />
        </div>

        <x-form-input name="logo_path" label="Logo path" :value="$team->logo_path" />

        <x-form-textarea name="description" label="Description" :value="$team->description" :rows="3" />

        <x-form-checkbox name="is_active" label="Active" :value="$team->is_active" />

        <div class="flex items-center justify-end gap-3 border-t pt-5">
            <a href="{{ route('admin.teams.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save changes</button>
        </div>
    </form>
@endsection