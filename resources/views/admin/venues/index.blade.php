@extends('layouts.admin', ['title' => 'Venues'])

@section('content')
    <div class="flex items-center justify-between mb-6">
        <form method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search venues..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <button class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm">Search</button>
        </form>
        @can('create', App\Models\Venue::class)
            <a href="{{ route('admin.venues.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">+ New Venue</a>
        @endcan
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($venues as $venue)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $venue->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $venue->city }}, {{ $venue->country }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $venue->capacity ? number_format($venue->capacity) : '—' }}</td>
                        <td class="px-6 py-4"><x-badge :color="$venue->is_active ? 'green' : 'gray'">{{ $venue->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('admin.venues.show', $venue) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                            <a href="{{ route('admin.venues.edit', $venue) }}" class="text-gray-700 hover:text-gray-900">Edit</a>
                            <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}" class="inline" onsubmit="return confirm('Delete this venue?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No venues found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $venues->links() }}</div>
@endsection