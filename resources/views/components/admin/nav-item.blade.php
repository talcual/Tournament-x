@props([
    'href' => '#',
    'active' => false,
])

@php
$baseClasses = 'flex items-center gap-3 px-3 py-2 rounded-md transition-colors';
$stateClasses = $active
    ? 'bg-slate-800 text-white'
    : 'text-slate-300 hover:bg-slate-800 hover:text-white';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $stateClasses"]) }}>
    <span class="text-base">{{ $icon ?? '•' }}</span>
    <span>{{ $slot }}</span>
</a>