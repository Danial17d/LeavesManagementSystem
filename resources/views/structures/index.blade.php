<x-auth-layout>
    <x-error/>
    <x-status/>
    <div class="max-w-5xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40 mb-10">
            <h1 class="text-3xl font-bold text-white mb-2">Organization Structure</h1>
            <p class="text-slate-400">View, add, and manage your company's organizational hierarchy</p>
        </div>

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            @if(!$hierarchical)
                <a href="{{ route('structures.create') }}" class="inline-flex items-center justify-center
                                        px-5 py-2 h-12
                                        bg-blue-600 text-white font-semibold
                                        rounded-lg
                                        transition duration-300 ease-in-out
                                        hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                    Add Root Node
                </a>
            @endif

            <x-divider/>

            <div class="bg-gray-900 p-6">
                @if($hierarchical)
                    @include('structures.partials.node-card', [
                        'node' => $hierarchical,
                        'nodesByParent' => $nodesByParent,
                        'depth' => 0
                    ])
                @else
                    <p class="text-gray-400">No structure found for your account yet.</p>
                @endif
            </div>
        </div>


    </div>

</x-auth-layout>
