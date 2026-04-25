<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">Create Leave Type</h1>
            <p class="text-slate-400 mb-8">Define leave type and approval flow settings.</p>

            <form method="POST" action="{{ route('leave-types.store') }}" class="space-y-4">
                @csrf

                <x-input label="Type" name="name" :value="old('name')"/>
                <x-input label="Days" name="days" type="number" :value="old('days')"/>
                <x-input label="Level" name="level" type="number" :value="old('level')"/>

                <div class="pt-4 flex gap-3">
                    <x-button>Create</x-button>
                    <a href="{{ route('leave-types.index') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
