<x-auth-layout>
    <x-error/>
    <x-status/>
    <div class="max-w-5xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <h1 class="text-3xl font-bold text-white ">Employees</h1>
            <div class="flex justify-between">
                <p class="text-gray-300  mt-2">Manage your Employee</p>
                <div class="space-x-3">
                    <x-button type="link" href="{{ route('users.create') }}">Create</x-button>
                    <x-button type="link" href="{{ route('structure.assignment.create', $structure) }}">Assign</x-button>
                    <x-button type="link" href="{{ route('structure.assignment.edit', $structure) }}">Move</x-button>
                </div>
            </div>
            <x-divider/>
            <h1 class="text-3xl font-bold text-white mb-2 ">Search</h1>
            <form method="GET" action="{{route('structures.show' , $structure->id)}}">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div class="md:col-span-4 w-full">
                        <x-input
                            name="search"
                            label="Search"
                            type="text"
                            placeholder="Employee"
                            class="w-full"
                        />
                    </div>

                    <div class="md:col-span-1 w-full">
                        <x-button class="w-full h-12">
                            Search
                        </x-button>
                    </div>
            </form>

            </div>


        </div>

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40 mt-10">

            <x-table title="Employees"
                     :headers="['id','name','email','role']"
                     :rows="$employees"

            >
                @foreach ($employees as $employee)
                    <tr class="hover:bg-gray-900/40 transition">
                        <td class="px-6 py-4 text-sm text-gray-200 text-center">{{ $employee->id }}</td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white text-center">{{ $employee->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white text-center">{{ $employee->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white text-center">{{ $employee->roles->first()->name ?? " no role assigned" }}</div>
                        </td>


                    </tr>
                @endforeach
            </x-table>
            </div>


    </div>
</x-auth-layout>
