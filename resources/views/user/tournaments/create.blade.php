@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Create a tournament</h1>
                <p class="mt-1 text-sm text-gray-600">Set up your own private tournament from your dashboard.</p>
            </div>

            <form method="POST" action="{{ route('user.tournaments.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-input name="name" label="Name" :required="true" />
                    <x-form-input name="slug" label="Slug" hint="Optional. Auto-generated from the name." />
                </div>

                <x-form-textarea name="description" label="Description" :rows="3" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-form-select name="sport_id" label="Sport" :options="\App\Models\Sport::orderBy('name')->get()->pluck('name', 'id')->toArray()" :required="true" />
                    <x-form-select name="format" label="Format" :options="collect(\App\Enums\TournamentFormat::cases())->mapWithKeys(fn($f) => [$f->value => $f->label()])->toArray()" :required="true" />
                    <x-form-select name="status" label="Status" :options="collect(\App\Enums\TournamentStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" :required="true" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-form-select name="participant_type" label="Participant type" :options="collect(\App\Enums\ParticipantType::cases())->mapWithKeys(fn($p) => [$p->value => ucfirst($p->value)])->toArray()" :required="true" />
                    <x-form-input name="min_participants" label="Min participants" type="number" :required="true" />
                    <x-form-input name="max_participants" label="Max participants" type="number" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-input name="starts_at" label="Starts at" type="date" />
                    <x-form-input name="ends_at" label="Ends at" type="date" />
                </div>

                <x-form-input name="registration_deadline" label="Registration deadline" type="datetime-local" />

                <div class="flex items-center justify-end gap-3 border-t pt-5">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Create tournament</button>
                </div>
            </form>
        </div>
    </div>
@endsection
