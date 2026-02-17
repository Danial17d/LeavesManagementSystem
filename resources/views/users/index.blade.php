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

                <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                    Create
                </a>
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

        <div id="overlay1" class="fixed inset-0 bg-opacity-50 z-40 hidden opacity-0 transition-opacity duration-300"></div>

        <div id="role-form" class="fixed inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:w-auto sm:min-w-[400px] sm:max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto bg-gray-800 text-white z-50 p-6 rounded-lg shadow-2xl hidden opacity-0 scale-95 transition-all duration-300 ease-in-out">
            <h2 class="text-2xl font-bold mb-6">Assign a role</h2>

            <form id="assign-role-form" method="POST" action="{{ route('role.assignment') }}">
                @csrf

                <input type="hidden" name="user_token" id="user_token">
                <x-input class="mb-4" label="User" name="selected_user" id="selected_user" readonly />

                <label for="role" class="block text-sm text-gray-300 mb-2">Role</label>
                <select id="role" name="role" class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                    <option value="" disabled selected>Select a role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" class="bg-gray-800">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-400 text-sm mt-2 mb-2">{{ $message }}</p>
                @enderror

                <div class="space-y-3 mt-6">
                    <x-button class="w-full">Assign</x-button>
                    <button type="button" class="close-modal w-full items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <x-table
                title="Employees"
                :headers="['ID','Name','Email','Role','Action']"
                :rows="$users">

                @foreach ($users as $user)
                    <tr class="hover:bg-gray-900/40 transition">
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

                                <button
                                    type="button"
                                    class="assign-role-btn px-3 py-1.5 text-sm rounded-lg bg-blue-700 hover:bg-blue-600 text-white transition"
                                    data-user-token="{{ encrypt((string) $user->id) }}"
                                    data-user-name="{{ $user->name }}"
                                    data-current-role="{{ $user->roles->first()?->name }}">
                                    Assign
                                </button>

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
        function renderAssignRoleModal() {
            const overlay = document.getElementById('overlay1');
            const modal = document.getElementById('role-form');
            const selectedUserInput = document.getElementById('selected_user');
            const roleSelect = document.getElementById('role');
            const closeButtons = document.querySelectorAll('.close-modal');
            const assignRoleButtons = document.querySelectorAll('.assign-role-btn');
            const userTokenInput = document.getElementById('user_token');

            function showModal() {
                overlay.classList.remove('hidden');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                    modal.classList.add('opacity-100', 'scale-100');
                }, 10);
            }

            function hideModal() {
                overlay.classList.remove('opacity-100');
                modal.classList.remove('opacity-100', 'scale-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    modal.classList.add('hidden');
                }, 300);
            }

            assignRoleButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const userToken = button.getAttribute('data-user-token');
                    const userName = button.getAttribute('data-user-name');
                    const currentRole = button.getAttribute('data-current-role');

                    userTokenInput.value = userToken;
                    selectedUserInput.value = userName;

                    if (currentRole) {
                        roleSelect.value = currentRole;
                    } else {
                        roleSelect.selectedIndex = 0;
                    }

                    showModal();
                });
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', hideModal);
            });

            overlay.addEventListener('click', hideModal);

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    hideModal();
                }
            });
        }

        renderAssignRoleModal();
    </script>
</x-auth-layout>
