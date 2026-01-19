@props(['type' => 'button'])

@if ($type === 'link')
    <a {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center
        px-6 py-3
        bg-blue-600 text-white font-semibold
        rounded-lg
        transition duration-300 ease-in-out
        hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg'
    ]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center
        px-6 py-3
        bg-blue-600 text-white font-semibold
        rounded-lg
        transition duration-300 ease-in-out
        hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg'
    ]) }}>
        {{ $slot }}
    </button>
@endif
