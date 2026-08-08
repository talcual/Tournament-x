@extends('layouts.admin', ['title' => 'New Tournament'])

@section('content')
    <form method="POST" action="{{ route('admin.tournaments.store') }}" class="max-w-3xl bg-white shadow rounded-lg p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input name="name" label="Name" :required="true" />
            <x-form-input name="slug" label="Slug" hint="Auto-generated if blank." />
        </div>

        <x-form-textarea name="description" label="Description" :rows="3" />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-select name="sport_id" label="Sport" :options="$sports->pluck('name', 'id')->toArray()" :required="true" />
            <x-form-select name="format" label="Format" :options="collect($formats)->mapWithKeys(fn($f) => [$f->value => $f->label()])->toArray()" :required="true" />
            <x-form-select name="status" label="Status" :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" :required="true" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-select name="participant_type" label="Participant type" :options="collect($participantTypes)->mapWithKeys(fn($p) => [$p->value => ucfirst($p->value)])->toArray()" :required="true" />
            <x-form-select name="organizer_id" label="Organizer" :options="$organizers->pluck('name', 'id')->toArray()" />
            <x-form-input name="registration_deadline" label="Registration deadline" type="datetime-local" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-form-input name="min_participants" label="Min participants" type="number" :required="true" />
            <x-form-input name="max_participants" label="Max participants" type="number" />
            <x-form-input name="starts_at" label="Starts at" type="date" />
            <x-form-input name="ends_at" label="Ends at" type="date" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Venues</label>
            <select name="venues[]" multiple size="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($venues as $venue)
                    <option value="{{ $venue->id }}">{{ $venue->name }} — {{ $venue->city }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple venues.</p>
        </div>

        <x-form-checkbox name="is_featured" label="Featured on public home" :value="false" />

        <div class="flex items-center justify-end gap-3 border-t pt-5">
            <a href="{{ route('admin.tournaments.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Create Tournament</button>
        </div>
    </form>
@endsection