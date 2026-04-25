<x-auth-layout>
    <x-error/>
    <x-status/>

    @php
        $currentPermissions = $role->permissions->pluck('name')->toArray();
        $groupedPermissions = collect($permissions)->groupBy(function ($permission) {
            return str($permission->value)->before(':')->replace('_', ' ')->title();
        });
    @endphp

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">Edit Role</h1>
            <p class="text-slate-400 mb-8">Update the role name and change its permissions.</p>

            <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <x-input label="Role" name="name" :value="old('name', $role->name)"/>

                <div>
                    <p class="block text-sm text-gray-300 mb-2 mt-4">Current Permissions</p>

                    @if (count($currentPermissions))
                        <div class="flex flex-wrap gap-2 rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            @foreach ($currentPermissions as $currentPermission)
                                <span class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1 text-sm font-medium text-white">
                                    {{ $currentPermission }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4 text-sm text-slate-400">
                            No permissions are currently assigned to this role.
                        </div>
                    @endif
                </div>

                <div>
                    <p class="block text-sm font-medium text-white mb-3 mt-4">Permissions</p>
                    <div class="rounded-xl border border-slate-600 bg-slate-700/40 p-5">
                        <div class="space-y-5">
                            @foreach ($groupedPermissions as $section => $sectionPermissions)
                                <div>
                                    <div class="mb-3 flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-white">{{ $section }}</h3>
                                        <span class="text-xs text-slate-300">{{ $sectionPermissions->count() }} permissions</span>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach ($sectionPermissions as $permission)
                                            <label class="flex items-center gap-3 rounded-lg border border-slate-600 bg-slate-700/60 px-4 py-4 text-gray-100 transition duration-300 ease-in-out hover:bg-slate-700 hover:border-blue-500/40">
                                                <input
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->value }}"
                                                    @checked(in_array($permission->value, old('permissions', $role->permissions->pluck('name')->toArray())))
                                                    class="h-4 w-4 rounded border-gray-500 bg-gray-100 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="text-sm">{{ $permission->value }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('permissions')
                        <p class="text-red-400 text-sm mt-2 mb-2">{{ $message }}</p>
                    @enderror
                    @error('permissions.*')
                        <p class="text-red-400 text-sm mt-2 mb-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 flex gap-3">
                    <x-button>Save changes</x-button>
                    <a href="{{ route('roles.show', $role) }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
