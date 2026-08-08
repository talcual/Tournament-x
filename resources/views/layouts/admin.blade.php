<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('app.admin.dashboard') }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-100 transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
        >
            <div class="px-6 py-5 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-lg font-semibold text-white">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600">🏆</span>
                    {{ __('app.app.name') }}
                </a>
                <p class="mt-1 text-xs text-slate-400 uppercase tracking-wide">{{ __('app.admin.panel') }}</p>
            </div>

            <nav class="px-3 py-4 space-y-1 text-sm">
                @php
                    $isAdmin = auth()->user()?->hasAnyRole(['admin', 'super-admin']);
                @endphp

                <x-admin.nav-item :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="🏠">
                    {{ __('app.admin.dashboard') }}
                </x-admin.nav-item>

                @if($isAdmin)
                    <x-admin.nav-item :href="route('admin.sports.index')" :active="request()->routeIs('admin.sports.*')" icon="🎯">
                        {{ __('app.admin.sports') }}
                    </x-admin.nav-item>
                @endif

                <x-admin.nav-item :href="route('admin.tournaments.index')" :active="request()->routeIs('admin.tournaments.*')" icon="🏆">
                    {{ __('app.admin.tournaments') }}
                </x-admin.nav-item>

                <x-admin.nav-item :href="route('admin.teams.index')" :active="request()->routeIs('admin.teams.*')" icon="👥">
                    {{ __('app.admin.teams') }}
                </x-admin.nav-item>

                <x-admin.nav-item :href="route('admin.players.index')" :active="request()->routeIs('admin.players.*')" icon="⭐">
                    {{ __('app.admin.players') }}
                </x-admin.nav-item>

                @if($isAdmin)
                    <x-admin.nav-item :href="route('admin.venues.index')" :active="request()->routeIs('admin.venues.*')" icon="📍">
                        {{ __('app.admin.venues') }}
                    </x-admin.nav-item>
                @endif

                @if(auth()->user()?->hasRole('super-admin'))
                    <x-admin.nav-item :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="🛡️">
                        {{ __('app.admin.users') }}
                    </x-admin.nav-item>
                @endif
            </nav>

            <div class="px-3 py-4 mt-auto border-t border-slate-800 text-xs">
                <form method="POST" action="{{ route('locale.switch') }}">
                    @csrf
                    <label class="block text-slate-400 uppercase tracking-wide mb-1">{{ __('app.language') }}</label>
                    <select name="locale" onchange="this.form.submit()" class="w-full rounded-md bg-slate-800 border-slate-700 text-slate-100 text-sm">
                        <option value="es" @selected(app()->getLocale() === 'es')>Español</option>
                        <option value="en" @selected(app()->getLocale() === 'en')>English</option>
                    </select>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = ! sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $title ?? __('app.admin.dashboard') }}</h1>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 hidden sm:block">{{ __('app.nav.view_site') }} →</a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = ! open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                                <span class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">{{ auth()->user()->getRoleNames()->first() }}</span>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black/5 py-1 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('app.nav.profile') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('app.nav.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                @if (session('status'))
                    <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>