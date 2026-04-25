<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">{{ $parentId ? 'Add new section' : 'Add root section' }}</h1>
            <p class="text-slate-400 mb-8">
                {{ $parentId ? 'Create a section under the selected structure.' : 'Create the root section for your organization structure.' }}
            </p>

            <form method="POST" action="{{ route('structures.store') }}" class="space-y-4">
                @csrf

                @if($parentId)
                    <input type="hidden" name="parent_id" value="{{ $parentId }}">
                @endif

                <x-input label="Name of the section" name="name" placeholder="IT" :value="old('name')"/>
                <x-input label="Type" name="type" placeholder="Department" :value="old('type')"/>

                <label for="manager_id" class="block text-sm text-gray-300 mb-2">Manager</label>
                <select id="manager_id" name="manager_id" class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                    <option value="" disabled selected>Select a manager</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" class="bg-gray-800" @selected(old('manager_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('manager_id')
                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror

                <div class="pt-4 flex gap-3">
                    <x-button>Create</x-button>
                    <a href="{{ route('structures.index') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
