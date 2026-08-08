@extends('layouts.admin', ['title' => __('app.admin.tournaments')])

@section('content')
    <div class="flex flex-col gap-3 mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('app.admin.tournaments') }}</h2>
            @can('create', App\Models\Tournament::class)
                <a href="{{ route('admin.tournaments.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">+ {{ __('app.admin.actions.new', ['resource' => __('app.admin.tournaments')]) }}</a>
            @endcan
        </div>

        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('app.admin.actions.search') }}" class="rounded-md border-gray-300 sm:text-sm">
            <select name="status" class="rounded-md border-gray-300 sm:text-sm">
                <option value="">{{ __('All') }}</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
            <select name="sport_id" class="rounded-md border-gray-300 sm:text-sm">
                <option value="">{{ __('All') }} · {{ __('app.admin.sports') }}</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" @selected(request('sport_id') == $sport->id)>{{ $sport->name }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm">{{ __('app.admin.actions.filter') }}</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.fields.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.fields.sport') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.fields.format') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.fields.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.fields.starts_at') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.fields.organizer') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.actions.edit') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tournaments as $tournament)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $tournament->name }}</div>
                            @if($tournament->is_featured)<span class="text-xs text-yellow-600">★ {{ __('app.public.featured') }}</span>@endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $tournament->sport->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $tournament->format->label() }}</td>
                        <td class="px-6 py-4"><x-badge :color="$tournament->status->color()">{{ $tournament->status->label() }}</x-badge></td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $tournament->starts_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $tournament->organizer->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('admin.tournaments.show', $tournament) }}" class="text-indigo-600 hover:text-indigo-800">{{ __('app.admin.actions.view') }}</a>
                            <a href="{{ route('admin.tournaments.matches', $tournament) }}" class="text-green-700 hover:text-green-900">{{ __('app.admin.matches.title') }}</a>
                            <a href="{{ route('admin.tournaments.draw', $tournament) }}" class="text-yellow-700 hover:text-yellow-900">{{ __('app.admin.draw.title') }}</a>
                            <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="text-gray-700 hover:text-gray-900">{{ __('app.admin.actions.edit') }}</a>
                            <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament) }}" class="inline" onsubmit="return confirm('Delete?');">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">{{ __('app.admin.actions.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">—</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tournaments->links() }}</div>
@endsection