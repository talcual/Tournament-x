@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
])

<div>
    <div class="flex items-start">
        <div class="flex h-5 items-center">
            <input
                type="hidden"
                name="{{ $name }}"
                value="0"
            />
            <input
                id="{{ $name }}"
                name="{{ $name }}"
                type="checkbox"
                value="1"
                @checked(old($name, $value))
                @if($required) required @endif
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            />
        </div>
        <div class="ml-3 text-sm">
            <label for="{{ $name }}" class="font-medium text-gray-700">{{ $label }}</label>
            @if($required)<span class="text-red-500">*</span>@endif
        </div>
    </div>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>