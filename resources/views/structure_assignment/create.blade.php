<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">Assign Employee</h1>
            <p class="text-slate-400 mb-8">
                Assign an employee to
                <span class="text-white font-semibold">{{ $structure->name }}</span>
                <span class="text-gray-400">({{ $structure->type }})</span>.
            </p>

            <form method="POST" action="{{ route('structure.assignment.store') }}" class="space-y-4">
                @csrf
                <input name="structure_id" type="hidden" value="{{ $structure->id }}">

                <label for="user_ids" class="block text-sm text-gray-300 mb-2">Users</label>
                <select id="user_ids" name="user_ids[]" multiple size="{{ min(max($users->count(), 6), 12) }}" class="w-full px-4 py-3 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @foreach ($users as $user)
                        <option value="{{ $user->uuid }}" class="bg-gray-800" @selected(collect(old('user_ids', []))->contains($user->uuid))>{{ $user->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400">Hold Ctrl or Cmd to select multiple employees.</p>

                @error('user_ids')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror

                <div class="pt-4 flex gap-3">
                    <x-button>Assign Users</x-button>
                    <a href="{{ route('structures.show', $structure) }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
