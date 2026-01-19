@props(['active' => false])
<a {{ $attributes->merge([
    'class' => $active
        ? 'text-xl px-2 pb-2 border-b-2  text-blue-600 cursor-pointer transition'
        : 'text-xl hover:bg-gray-500 rounded-lg px-3 py-2 transition duration-300 ease-in-out'
]) }}>
    {{ $slot }}
</a>
