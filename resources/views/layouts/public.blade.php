<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('app.app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-semibold text-indigo-700">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white">🏆</span>
                {{ __('app.app.name') }}
            </a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">{{ __('app.nav.tournaments') }}</a>
                @auth
                    @if(auth()->user()->hasAnyRole(['admin','organizer','referee','super-admin']))
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">{{ __('app.nav.admin') }}</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-gray-900">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-gray-600 hover:text-gray-900">{{ __('app.nav.logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">{{ __('app.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="px-3 py-2 bg-indigo-600 text-white rounded-md">{{ __('app.nav.register') }}</a>
                @endauth
                <form method="POST" action="{{ route('locale.switch') }}" class="inline">
                    @csrf
                    <select name="locale" onchange="this.form.submit()" class="rounded-md border-gray-300 text-sm">
                        <option value="es" @selected(app()->getLocale() === 'es')>ES</option>
                        <option value="en" @selected(app()->getLocale() === 'en')>EN</option>
                    </select>
                </form>
            </nav>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-500 flex justify-between">
            <span>&copy; {{ date('Y') }} {{ __('app.app.name') }}</span>
            <span>Built with Laravel 13</span>
        </div>
    </footer>
</body>
</html>