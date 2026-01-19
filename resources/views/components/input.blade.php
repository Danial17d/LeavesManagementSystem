@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
])

<div>
    <label for="{{ $name }}" class="block text-sm text-gray-300 mb-2">
        {{ $label }}
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => '
                w-full rounded-lg
                bg-gray-900 border border-gray-700
                text-white px-4 py-3
                outline-none
                focus:ring-2 focus:ring-blue-500
                focus:ring-offset-2 focus:ring-offset-gray-800
            '
        ]) }}
    >

    @error($name)
    <p class="text-red-400 text-sm mt-2">
        {{ $message }}
    </p>
    @enderror
</div>
