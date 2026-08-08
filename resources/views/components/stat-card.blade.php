@props([
    'title' => '',
    'subtitle' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white shadow rounded-lg p-5']) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $slot }}</p>
            @if($subtitle)
                <p class="mt-1 text-xs text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
        @isset($icon)
            <div class="text-indigo-500">
                {{ $icon }}
            </div>
        @endisset
    </div>
</div>