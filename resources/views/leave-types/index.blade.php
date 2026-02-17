<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-5xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40 mb-10">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Leave Types</h1>
                    <p class="text-slate-400">Manage leave types, allowed days, and approval steps.</p>
                </div>
                @can(\App\Enums\PermissionType::LeaveTypeCreate)
                    <a href="{{ route('leave-types.create') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Create
                    </a>
                @endcan
            </div>
            <x-divider/>

            <form method="GET" action="{{ route('leave-types.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mt-6">
                <div>
                    <label for="search" class="block text-sm text-gray-300 mb-2">Search</label>
                    <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="Leave type"
                           class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 h-12 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="sort" class="block text-sm text-gray-300 mb-2">Sort</label>
                    <select id="sort" name="sort" class="w-full h-12 px-4 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id" @selected(request('sort', 'id') === 'id')>ID</option>
                        <option value="name" @selected(request('sort') === 'name')>Type</option>
                        <option value="days" @selected(request('sort') === 'days')>Days</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <x-button>Apply</x-button>
                    <a href="{{ route('leave-types.index') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <x-table title="Leave Types" :headers="['ID', 'Type', 'Days', 'Level', 'Action']" :rows="$leaveTypes">
                @foreach ($leaveTypes as $leaveType)
                    <tr class="hover:bg-gray-900/40 transition">
                        <td class="px-6 py-4 text-sm text-gray-200 text-center">{{ $leaveType->id }}</td>
                        <td class="px-6 py-4 text-sm text-white text-center">{{ $leaveType->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-300 text-center">{{ $leaveType->days }}</td>
                        <td class="px-6 py-4 text-sm text-gray-300 text-center">{{ $leaveType->approvalRule?->level ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('leave-types.show', $leaveType) }}" class="px-3 py-1.5 text-sm rounded-lg bg-gray-700 hover:bg-gray-600 text-white transition">Show</a>
                                <a href="{{ route('leave-types.edit', $leaveType) }}" class="px-3 py-1.5 text-sm rounded-lg bg-blue-700 hover:bg-blue-600 text-white transition">Edit</a>
                                <form method="POST" action="{{ route('leave-types.destroy', $leaveType) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this leave type?')" class="px-3 py-1.5 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
    </div>
</x-auth-layout>
