@extends('layouts.admin', ['title' => 'Edit Venue'])

@section('content')
    <form method="POST" action="{{ route('admin.venues.update', $venue) }}" class="max-w-2xl bg-white shadow rounded-lg p-6 space-y-5">
        @csrf @method('PUT')
        <x-form-input name="name" label="Name" :value="$venue->name" :required="true" />
        <x-form-input name="address" label="Address" :value="$venue->address" :required="true" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input name="city" label="City" :value="$venue->city" :required="true" />
            <x-form-input name="country" label="Country" :value="$venue->country" :required="true" />
        </div>

        <x-form-input name="capacity" label="Capacity" type="number" :value="$venue->capacity" />

        <x-form-checkbox name="is_active" label="Active" :value="$venue->is_active" />

        <div class="flex items-center justify-end gap-3 border-t pt-5">
            <a href="{{ route('admin.venues.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancel</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Save changes</button>
        </div>
    </form>
@endsection