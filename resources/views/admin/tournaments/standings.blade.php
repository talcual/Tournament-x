@extends('layouts.admin', ['title' => __('app.admin.standings.title').' · '.$tournament->name])

@section('content')
    <a href="{{ route('admin.tournaments.matches', $tournament) }}" class="text-sm text-indigo-600 hover:text-indigo-800">← {{ __('app.admin.matches.title') }}</a>

    <div class="mt-4 max-w-4xl">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('app.admin.standings.title') }} — {{ $tournament->name }}</h1>

        <div class="mt-4 bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.pos') }}</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.participant') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.played') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.wins') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.draws') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.losses') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.goals_for') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.goals_against') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.diff') }}</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('app.admin.standings.points') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($standings as $row)
                        <tr>
                            <td class="px-3 py-2 text-sm font-medium text-gray-900">{{ $row->position }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $row->participant->name ?? $row->participant->full_name ?? '—' }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ $row->played }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ $row->wins }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ $row->draws }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ $row->losses }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ $row->goals_for }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ $row->goals_against }}</td>
                            <td class="px-3 py-2 text-sm text-center">{{ $row->goalDifference() }}</td>
                            <td class="px-3 py-2 text-sm text-center font-semibold">{{ $row->points }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-3 py-6 text-center text-gray-500">{{ __('app.public.no_standings') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection