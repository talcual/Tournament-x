@extends('layouts.admin', ['title' => $venue->name])

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl">
        <h2 class="text-xl font-semibold text-gray-900">{{ $venue->name }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $venue->address }}, {{ $venue->city }}, {{ $venue->country }}</p>

        <dl class="grid grid-cols-2 gap-4 mt-6 text-center">
            <div class="p-4 bg-gray-50 rounded-md">
                <dt class="text-xs text-gray-500 uppercase">Capacity</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $venue->capacity ? number_format($venue->capacity) : '—' }}</dd>
            </div>
            <div class="p-4 bg-gray-50 rounded-md">
                <dt class="text-xs text-gray-500 uppercase">Status</dt>
                <dd class="mt-2"><x-badge :color="$venue->is_active ? 'green' : 'gray'">{{ $venue->is_active ? 'Active' : 'Inactive' }}</x-badge></dd>
            </div>
        </dl>
    </div>
@endsection