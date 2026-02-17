@php
    $indent = $depth * 20;
@endphp

<div class="mb-3" style="margin-left: {{ $indent }}px;">
    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 hover:border-blue-500 transition duration-200">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-white font-semibold text-lg">{{ $node->name }}</h3>
                <div class="mt-1 text-sm text-gray-300 flex flex-wrap gap-3">
                    <span>{{ $node->type }}</span>
                    <span>Manager: {{ $node->manager?->name ?? 'Unassigned' }}</span>
                    <span>{{ $node->users_count ?? 0 }} {{ \Illuminate\Support\Str::plural('user', $node->users_count ?? 0) }}</span>
                    <span class="text-gray-500">Level {{ substr_count((string) $node->path, '.') + 1 }}</span>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="/structures/{{ $node->id }}"
                   class="px-3 py-1 bg-blue-600 text-white rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg inline-flex items-center">
                    Show
                </a>

                <button type="button"
                        class="add-child-btn px-3 py-1 bg-green-600 rounded-lg transition duration-300 ease-in-out hover:bg-green-700 hover:-translate-y-0.5 hover:shadow-lg"
                        data-parent-id="{{ $node->id }}">
                    Add
                </button>

                <form action="/structures/{{ $node->id }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this structure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 bg-red-600 rounded-lg transition duration-300 ease-in-out hover:bg-red-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach(($nodesByParent[$node->id] ?? collect()) as $child)
    @include('structures.partials.node-card', [
        'node' => $child,
        'nodesByParent' => $nodesByParent,
        'depth' => $depth + 1
    ])
@endforeach
