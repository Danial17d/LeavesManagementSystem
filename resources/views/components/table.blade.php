@props([
    'title' => null,
    'subtitle' => null,
    'headers' => [],
    'rows' => null,
    'empty' => 'No results found.',
    'paginate' => true,
])

<div {{ $attributes->merge(['class' => 'mt-6 bg-gray-800/40 border border-gray-700/60 rounded-xl shadow-lg overflow-hidden']) }}>
    @if($title || $subtitle)
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                @if($title)
                    <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
                @endif

                @if($subtitle)
                    <p class="text-sm text-gray-400">{{ $subtitle }}</p>
                @elseif($rows instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <p class="text-sm text-gray-400">
                        Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }}
                    </p>
                @endif
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-900/50">
            <tr class="text-left text-sm text-gray-300">
                @foreach($headers as $header)
                    <th class="px-6 py-3 font-medium">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-700/60">
            @if($rows && count($rows))
                {{ $slot }}
            @else
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-10 text-center text-gray-400">
                        {{ $empty }}
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
        @if($paginate && $rows instanceof \Illuminate\Pagination\LengthAwarePaginator && $rows->hasPages())
            <div class="px-6 py-4 border-t border-gray-700/60">
                {{ $rows->withQueryString()->links() }}
            </div>
        @endif
</div>
