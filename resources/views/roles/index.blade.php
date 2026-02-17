<x-auth-layout>
    <div class="max-w-5xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <h1 class="text-3xl font-bold text-white ">Roles</h1>
            <div class="flex justify-between">
                <p class="text-gray-300  mt-2">List and manage your roles</p>
                <x-button type="link" href="/roles/create">Create</x-button>
            </div>

            <x-divider/>

            <h1 class="text-3xl font-bold text-white mb-4 ">Filter</h1>
            <form method="get" action="/roles">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

                    <!-- Role -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                        <div class="relative">
                            <select name="role_name"
                                    class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg
                       focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                                <option value="" disabled selected>Select a role</option>
                                @foreach ($roleName as $role)
                                    <option value="{{ $role->name }}" class="bg-gray-800">{{ $role->name }}</option>
                                @endforeach
                            </select>

                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="w-full">
                        <x-input label="Search" name="search" placeholder="User" />
                    </div>

                    <!-- Sort by -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sort by</label>
                        <div class="relative">
                            <select name="sort_by"
                                    class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg
                       focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                                <option value="created_at" class="bg-gray-800">Newest</option>
                                <option value="id" class="bg-gray-800">ID</option>
                                <option value="name" class="bg-gray-800">Name</option>
                            </select>

                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Order -->
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Order</label>
                        <div class="relative">
                            <select name="sort_dir"
                                    class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg
                       focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                                <option value="desc" class="bg-gray-800">Desc</option>
                                <option value="asc" class="bg-gray-800">Asc</option>
                            </select>

                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="w-full">
                        <button
                            type="submit"
                            class="w-full h-12 inline-flex items-center justify-center
                   bg-blue-600 text-white font-semibold rounded-lg
                   transition duration-300 ease-in-out
                   hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                            Filter
                        </button>
                    </div>

                </div>



            </form>
        </div>
        <div class="bg-gray-800 rounded-lg p-8 mt-10
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <x-table
                title="Roles"
                :headers="['ID', 'Name', 'Created At', 'Actions']"
                :rows="$roles">
                @foreach ($roles as $role)
                    <tr class="hover:bg-gray-900/40 transition">
                        <td class="px-6 py-4 text-sm text-gray-200">{{ $role->id }}</td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white text-center">{{ $role->name }}</div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-300 text-center">
                            {{ \Carbon\Carbon::parse($role->created_at)->format('Y F d') }}
                        </td>

                        <td class="px-6 py-4 text-center text-center">
                            <div class="inline-flex items-center gap-2">
                                <a href="#"
                                   class="px-3 py-1.5 text-sm rounded-lg bg-gray-700 hover:bg-gray-600 text-white transition">
                                    Edit
                                </a>

                                <form method="POST" action="#">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white transition">
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
</x-auth-layout>

