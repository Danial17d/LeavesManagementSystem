<x-layout2>
    <x-error/>
    <x-status/>
    <div id="overlay1" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden opacity-0 transition-opacity duration-300"></div>
    <div id="assign-user" class="fixed inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2
                            sm:w-auto sm:min-w-[400px] sm:max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto
                            bg-gray-800 text-white z-50 p-6 rounded-lg shadow-2xl
                            hidden opacity-0 scale-95
                            transition-all duration-300 ease-in-out">
        <h1 class="text-2xl font-bold mb-6">Assign new user to the structure</h1>
        <form action="/user-assignment" method="post">
            @csrf
            <input name="structure_id" type="hidden" value="{{request()->route('structure')->id}}">
            <label for="user_name" class="block text-sm text-gray-300 mb-2">User Name</label>
            <select id="user_name" name="user_name" class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                <option value="" disabled selected>Select a user</option>
                @foreach ($users as $user)
                    <option value="{{ $user->name }}" class="bg-gray-800">{{ $user->name}}</option>
                @endforeach
            </select>
            @error('user_name')
            <p class="text-red-400 text-sm mt-2 mb-2">
                {{ $message }}
            </p>
            @enderror

            <div class="space-y-3 mt-6">
                <x-button class="w-full">Create</x-button>
                <button type="button" class="close-modal w-full items-center justify-center
                                        px-5 py-2 h-12
                                        bg-gray-600 text-white font-semibold
                                        rounded-lg
                                        transition duration-300 ease-in-out
                                        hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                    Cancel
                </button>
            </div>


    </form>
    </div>
    <div class="max-w-5xl mx-auto px-4 py-16">
        <div id="move-user" class="fixed inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2
                            sm:w-auto sm:min-w-[400px] sm:max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto
                            bg-gray-800 text-white z-50 p-6 rounded-lg shadow-2xl
                            hidden opacity-0 scale-95
                            transition-all duration-300 ease-in-out">

        </div>

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <h1 class="text-3xl font-bold text-white ">Employees</h1>
            <div class="flex justify-between">
                <p class="text-gray-300  mt-2">Manage your Employee</p>
                <div class="space-x-3">
                    <x-button type="link" href="/users/create">Create</x-button>
                    <x-button id="assign">Assign</x-button>
                    <x-button id="move">Move</x-button>
                </div>
            </div>
            <x-divider/>
            <h1 class="text-3xl font-bold text-white mb-2 ">Search</h1>
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
                        <td class="px-6 py-4 text-sm text-gray-200">{{ $employee->id }}</td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white">{{ $employee->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white">{{ $employee->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white">{{ $employee->roles->first()->name ?? " no role assigned" }}</div>
                        </td>


                    </tr>
                @endforeach
            </x-table>
            </div>


    </div>
    <script>
        function renderAssignForm(){
            const overlay1 = document.getElementById('overlay1');
            const assignUser = document.getElementById('assign-user');
            const moveUser = document.getElementById('move-user')
            const assignBtn = document.getElementById('assign')
            const moveBtn = document.getElementById('move')
            const closeButtons = document.querySelectorAll('.close-modal');

            function showModal(modalElement) {
                overlay1.classList.remove('hidden');
                modalElement.classList.remove('hidden');
                setTimeout(() => {
                    overlay1.classList.add('opacity-100');
                    modalElement.classList.add('opacity-100', 'scale-100');
                }, 10);
            }

            function hideModal(modalElement) {
                overlay1.classList.remove('opacity-100');
                modalElement.classList.remove('opacity-100', 'scale-100');
                setTimeout(() => {
                    overlay1.classList.add('hidden');
                    modalElement.classList.add('hidden');
                }, 300);
            }

            function hideAllModals() {
                hideModal(assignUser);
                hideModal(moveUser);
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-child-btn')) {
                    document.getElementById('parent_id').value = e.target.getAttribute('data-parent-id');
                    showModal(moveUser);
                }
            });


            if (assignBtn) {
                assignBtn.addEventListener('click', () => showModal(assignUser));
            }
            if(moveBtn){
                moveBtn.addEventListener('click',()=>showModal(moveUser));
            }


            closeButtons.forEach(button => {
                button.addEventListener('click', hideAllModals);
            });


            if (overlay1) {
                overlay1.addEventListener('click', hideAllModals);
            }


            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    hideAllModals();
                }
            });
        }
        renderAssignForm();
    </script>
</x-layout2>
