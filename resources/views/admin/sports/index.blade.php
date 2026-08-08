@extends('layouts.admin', ['title' => 'Sports'])

@section('content')
    <div class="flex items-center justify-between mb-6">
        <form method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search sports..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <button class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm">Search</button>
        </form>
        @can('create', App\Models\Sport::class)
            <a href="{{ route('admin.sports.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">+ New Sport</a>
        @endcan
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sport</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tournaments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teams</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sports as $sport)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $sport->icon ?? '🎯' }}</span>
                                <span class="font-medium text-gray-900">{{ $sport->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sport->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge :color="$sport->is_team_sport ? 'blue' : 'indigo'">{{ $sport->is_team_sport ? 'Team' : 'Individual' }}</x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sport->tournaments_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sport->teams_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('admin.sports.show', $sport) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                            @can('update', App\Models\Sport::class)
                                <a href="{{ route('admin.sports.edit', $sport) }}" class="text-gray-700 hover:text-gray-900">Edit</a>
                            @endcan
                            @can('delete', App\Models\Sport::class)
                                <form method="POST" action="{{ route('admin.sports.destroy', $sport) }}" class="inline" onsubmit="return confirm('Delete this sport?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No sports found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sports->links() }}</div>
@endsection