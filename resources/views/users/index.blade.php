<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-5xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40 mb-10">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Users and Employees</h1>
                    <p class="text-slate-400">Manage user accounts, employee profiles, roles and structure in one place.</p>
                </div>

              <div>
                  <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                      Create
                  </a>
                  <a href="{{ route('roles.index') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                      Roles
                  </a>
              </div>
            </div>
            <x-divider/>
            <h2 class="text-2xl font-bold text-white mt-8 mb-2">Filter</h2>

            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end mt-4">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm text-gray-300 mb-2">Search</label>
                    <input
                        id="search"
                        name="search"
                        type="text"
                        value="{{ request('search') }}"
                        placeholder="Name or email"
                        class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 h-12 outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="md:col-span-3">
                    <label for="role-filter" class="block text-sm text-gray-300 mb-2">Role</label>
                    <select id="role-filter" name="role" class="w-full h-12 px-4 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label for="sort" class="block text-sm text-gray-300 mb-2">Sort</label>
                    <select id="sort" name="sort" class="w-full h-12 px-4 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id" @selected(request('sort', 'id') === 'id')>ID</option>
                        <option value="name" @selected(request('sort') === 'name')>Name</option>
                        <option value="email" @selected(request('sort') === 'email')>Email</option>
                    </select>
                </div>

                <div class="md:col-span-4">
                    <label for="dir" class="block text-sm text-gray-300 mb-2">Direction</label>
                    <select id="dir" name="dir" class="w-full h-12 px-4 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="asc" @selected(request('dir', 'asc') === 'asc')>Ascending</option>
                        <option value="desc" @selected(request('dir') === 'desc')>Descending</option>
                    </select>
                </div>

                <div class="md:col-span-12 flex gap-3">
                    <x-button>Apply filters</x-button>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <form id="bulk-role-form" method="POST" action="{{ route('role.assignment') }}">
                @csrf
                <div id="selected-user-ids"></div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-6">
                    <div class="md:col-span-6">
                        <label for="bulk-role" class="block text-sm text-gray-300 mb-2">Assign role to selected users</label>
                        <select id="bulk-role" name="role" class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition" required>
                            <option value="" disabled selected>Select a role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" class="bg-gray-800" @selected(old('role') === $role->name)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        @error('user_ids')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        @error('user_ids.*')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 flex gap-3 md:justify-end">
                        <button type="button" id="select-all-users" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                            Select all
                        </button>
                        <button type="button" id="clear-users" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                            Clear
                        </button>
                        <x-button class="h-12">Assign Selected</x-button>
                    </div>
                </div>
            </form>

            <x-table
                title="Employees"
                :headers="['Select','ID','Name','Email','Role','Action']"
                :rows="$users">

                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-900/40 transition">
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    name="user_ids[]"
                                    value="{{ $user->id }}"
                                    @checked(collect(old('user_ids', []))->contains((string) $user->id))
                                    class="bulk-user-checkbox h-4 w-4 rounded border-gray-600 bg-gray-900 text-blue-600 focus:ring-blue-500"
                                >
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-200 text-center">{{ $user->id }}</td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-white text-center">{{ $user->name }}</div>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-300 text-center">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-300 text-center" >
                                {{ $user->roles->pluck('name')->join(', ') ?: 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" class="px-3 py-1.5 text-sm rounded-lg bg-gray-700 hover:bg-gray-600 text-white transition">
                                        Show
                                    </a>

                                    <form method="POST" action="{{ route('users.destroy', $user) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this user?')" class="px-3 py-1.5 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
            </x-table>
        </div>
    </div>

    <script>
        function setupBulkRoleSelection() {
            const selectAllBtn = document.getElementById('select-all-users');
            const clearBtn = document.getElementById('clear-users');
            const bulkForm = document.getElementById('bulk-role-form');
            const selectedUserIdsContainer = document.getElementById('selected-user-ids');

            if (!selectAllBtn || !clearBtn || !bulkForm || !selectedUserIdsContainer) {
                return;
            }

            const getCheckboxes = () => document.querySelectorAll('.bulk-user-checkbox');
            const getCheckedValues = () => Array.from(getCheckboxes())
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value);

            selectAllBtn.addEventListener('click', () => {
                getCheckboxes().forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            clearBtn.addEventListener('click', () => {
                getCheckboxes().forEach((checkbox) => {
                    checkbox.checked = false;
                });
            });

            bulkForm.addEventListener('submit', (event) => {
                const checkedUserIds = getCheckedValues();

                selectedUserIdsContainer.innerHTML = '';

                checkedUserIds.forEach((userId) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = userId;
                    selectedUserIdsContainer.appendChild(input);
                });

                if (checkedUserIds.length === 0) {
                    event.preventDefault();
                    alert('Please select at least one user.');
                }
            });
        }

        setupBulkRoleSelection();
    </script>
</x-auth-layout>
