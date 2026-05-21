<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-6xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40 mb-10">
            <h1 class="text-3xl font-bold text-white mb-2">Move Employees</h1>
            <p class="text-slate-400">
                Current structure:
                <span class="text-white font-semibold">{{ $structure->name }}</span>
                <span class="text-gray-400">({{ $structure->type }})</span>
            </p>
            <div class="mt-4">
                <a href="{{ route('structures.show', $structure) }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                    Back to structure
                </a>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <x-table title="Employees in Current Structure" :headers="['Name','Email','Role','Move User']" :rows="$employees">
                @foreach ($employees as $employee)
                    <tr class="hover:bg-gray-900/40 transition">
                        <td class="px-6 py-4 text-sm text-white text-center">{{ $employee->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-300 text-center">{{ $employee->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-300 text-center">{{ $employee->roles->pluck('name')->join(', ') ?: 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('structure.assignment.update') }}" class="grid grid-cols-1 md:grid-cols-6 gap-2 items-center">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="user_id" value="{{ $employee->uuid }}">
                                <input type="hidden" name="from_structure_id" value="{{ $structure->id }}">

                                <div class="md:col-span-5">
                                    <select name="to_structure_id" class="w-full h-11 px-3 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="" selected disabled>Select destination structure</option>
                                        @foreach ($targetStructures as $target)
                                            <option value="{{ $target->id }}">{{ $target->name }} ({{ $target->type }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-1">
                                    <button type="submit" class="w-full px-3 py-2.5 text-sm rounded-lg bg-blue-700 hover:bg-blue-600 text-white transition">Move</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
    </div>
</x-auth-layout>
