<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            @can('create', App\Models\Tournament::class)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Create your tournament</h3>
                                <p class="text-sm text-gray-600">Launch a new competition from your private dashboard.</p>
                            </div>
                            <a href="{{ route('user.tournaments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500">
                                + New tournament
                            </a>
                        </div>
                    </div>
                </div>
            @endcan

            @php
                $myTournaments = auth()->user()->organizedTournaments()->latest()->take(5)->get();
            @endphp

            @if($myTournaments->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your tournaments</h3>
                        <div class="space-y-3">
                            @foreach($myTournaments as $tournament)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $tournament->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $tournament->status->label() ?? $tournament->status }}</div>
                                    </div>
                                    <a href="{{ route('public.tournaments.show', $tournament) }}" class="text-sm text-indigo-600 hover:text-indigo-500">View</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
