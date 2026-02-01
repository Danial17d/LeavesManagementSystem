<x-layout2>
    <div class="max-w-4xl mx-auto mt-10 mb-10">

        <div class="bg-slate-800 rounded-lg p-8 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-3xl font-bold text-white">Create Role</h1>
                <button onclick="window.history.back()"
                        class="px-4 py-2 text-slate-400 hover:text-white transition-colors">
                    ← Back to Roles
                </button>
            </div>
            <p class="text-slate-400">Add a new role to your system</p>
        </div>

        <div class="bg-slate-800 rounded-lg p-8">
            <form id="roleForm" class="space-y-6">

                <div>
                    <x-input
                        label="role"
                        type="text"
                        id="roleName"
                        name="roleName"
                        placeholder="Enter role name"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-3">
                        Users
                    </label>
                    <select name="user_name" class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                        <option value="" disabled selected>Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}" class="bg-gray-800">{{ $user->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-3">
                        Permissions
                    </label>

                    <div class="bg-slate-700/60 border border-slate-600 rounded-xl p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                                <label
                                    for="perm_{{ $permission->value }}"
                                    class="flex items-center gap-3 px-4 py-3 rounded-lg
                           bg-slate-800/40 hover:bg-slate-800/70
                           border border-slate-600/40
                           cursor-pointer transition"
                                >
                                    <input
                                        type="checkbox"
                                        id="perm_{{ $permission->value }}"
                                        name="permissions[]"
                                        value="{{ $permission->value }}"
                                        class="w-4 h-4 rounded border-slate-500 bg-slate-700
                               text-blue-600 focus:ring-2 focus:ring-blue-500"
                                    />

                                    <span class="text-slate-200 text-sm">
                                        {{ $permission->value }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="flex gap-3 pt-4">
                    <x-button class="w-full">Create a role</x-button>
                    <a
                        href="/roles"
                        class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:ring-offset-slate-800"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout2>
