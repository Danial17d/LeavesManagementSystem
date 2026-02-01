
@props(['item', 'depth' => 0])

@php
    $indent = $depth * 20;
    $bgColor = match($depth) {
        0 => 'bg-gray-700',
        1 => 'bg-gray-800',
        default => 'bg-gray-600'
    };
@endphp

<div class="mb-2" style="margin-left: {{ $indent }}px">
    <div class="{{ $bgColor }} rounded-lg p-4 border border-gray-600 hover:border-blue-500 transition-all duration-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <h3 class="text-white font-semibold text-lg">{{ $item->name }}</h3>
                    <div class="flex items-center gap-4 text-sm">
                        <p class="text-gray-400 text-sm">{{ $item->type }}</p>
                        @if($item->manager->name)
                            <span class="text-gray-300">
                                <span class="text-gray-500">Manager:</span> {{$item->manager->name}}
                            </span>
                        @endif

                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full">
                            {{ $item['users_count'] }} {{ Str::plural('user', $item->users) }}
                        </span>

                        <span class="text-gray-500 text-xs">
                            Level {{ count( explode('.', $item->path)) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">

                <a href="/structures/{{ $item->id }}"
                   class="px-3 py-1 bg-blue-600 text-white rounded-lg transition duration-300 ease-in-out
              hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg inline-flex items-center">
                    Show
                </a>


                <button type="button"
                        class="add-child-btn px-3 py-1 bg-green-600 rounded-lg transition duration-300 ease-in-out
                   hover:bg-green-700 hover:-translate-y-0.5 hover:shadow-lg"
                        data-parent-id="{{ $item->id }}">
                    Add
                </button>

                <form action="/structures/{{ $item->id }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this structure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 bg-red-600 rounded-lg transition duration-300 ease-in-out
                                       hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Delete
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

@if(isset($item['children']) && count($item['children']) > 0)
    @foreach($item['children'] as $child)
        <x-hierarchy-item :item="$child" :depth="$depth + 1" />
    @endforeach
@endif
