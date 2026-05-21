<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">Request Structure Transfer</h1>
            <p class="text-slate-400 mb-8">Submit a request to be transferred to a different department or structure.</p>

            <form method="POST" action="{{ route('structure-requests.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm text-gray-300 mb-2">Current Structure</label>
                    <div class="w-full rounded-lg bg-gray-900/50 border border-gray-700 text-slate-400 px-4 py-3 h-12 flex items-center">
                        {{ $currentStructure?->name ?? 'Not assigned to any structure' }}
                    </div>
                </div>
                <div>
                    <input type="hidden" name="type" value="{{ $isAssign ? 'move' : 'assign' }}">
                    <x-input name="type_display" label="Transfer Type" value="{{ $isAssign ? 'Move to structure' : 'Assign to structure' }}" disabled/>
                    @error('type')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                @if(auth()->user()->hasRole(\App\Enums\UserRole::Employee->value))
                    <div>
                        <label for="structure_id" class="block text-sm text-gray-300 mb-2">Transfer To</label>
                        <select id="structure_id" name="structure_id"
                                class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 h-12 outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 appearance-none">
                            <option value="" disabled {{ old('structure_id') ? '' : 'selected' }}>Select a structure</option>
                            @foreach ($structures as $structure)
                                <option value="{{ $structure->id }}" {{ old('structure_id') == $structure->id ? 'selected' : '' }}>
                                    {{ $structure->name }}
                                    @if ($structure->type)
                                        &mdash; {{ $structure->type }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('structure_id')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                @endif
                @if(auth()->user()->hasRole(\App\Enums\UserRole::Admin))
                    <div>
                        <input type="hidden" name="structure_id" value="{{ $structure->parent->id ?? '' }}">
                        <x-input name="transfer_to" label="Transfer To" value="{{ $structure->parent->name ?? 'No Parent' }}" disabled/>
                        @error('structure_id')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="reason" class="block text-sm text-gray-300 mb-2">Reason <span class="text-slate-500">(optional)</span></label>
                    <textarea id="reason" name="reason" rows="4"
                              placeholder="Briefly explain why you are requesting this transfer."
                              class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 resize-none">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Submit Request
                    </button>
                    <a href="{{ route('structure-requests.index') }}"
                       class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
