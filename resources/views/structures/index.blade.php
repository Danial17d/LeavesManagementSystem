<x-auth-layout>
    <x-error/>
    <x-status/>
    <div class="max-w-5xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40 mb-10">
            <h1 class="text-3xl font-bold text-white mb-2">Organization Structure</h1>
            <p class="text-slate-400">View, add, and manage your company's organizational hierarchy</p>
        </div>

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            @if(!$hierarchical)
                <button type="button" id="root-node" class="items-center justify-center
                                        px-5 py-2 h-12
                                        bg-blue-600 text-white font-semibold
                                        rounded-lg
                                        transition duration-300 ease-in-out
                                        hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                    Add Root Node
                </button>
            @endif

            <x-divider/>

            <div class="bg-gray-900 p-6">
                @if($hierarchical)
                    @include('structures.partials.node-card', [
                        'node' => $hierarchical,
                        'nodesByParent' => $nodesByParent,
                        'depth' => 0
                    ])
                @else
                    <p class="text-gray-400">No structure found for your account yet.</p>
                @endif
            </div>
        </div>


        <div id="overlay1" class="fixed inset-0 bg-opacity-50 z-40 hidden opacity-0 transition-opacity duration-300"></div>

        <div id="root-form" class="fixed inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2
                            sm:w-auto sm:min-w-[400px] sm:max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto
                            bg-gray-800 text-white z-50 p-6 rounded-lg shadow-2xl
                            hidden opacity-0 scale-95
                            transition-all duration-300 ease-in-out">
            <h2 class="text-2xl font-bold mb-6">Create Node</h2>

            <form action="/structures" method="post">
                @csrf
                <x-input class="mb-4" label="Name of the section" name="name" placeholder="IT"/>
                <x-input class="mb-4" label="Type" name="type" placeholder="Department"/>

                <label for="manager_id" class="block text-sm text-gray-300 mb-2">Manager</label>
                <select id="manager_id" name="manager_id" class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                    <option value="" disabled selected>Select a manager</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" class="bg-gray-800">{{ $user->name}}</option>
                    @endforeach
                </select>
                @error('manager_id')
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
{{--        sm mean when the page size is greater then 640px take the action--}}
        <div id="child-form" class="fixed inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2
                            sm:w-auto sm:min-w-[400px] sm:max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto
                            bg-gray-800 text-white z-50 p-6 rounded-lg shadow-2xl
                            hidden opacity-0 scale-95
                            transition-all duration-300 ease-in-out">
            <h2 class="text-2xl font-bold mb-6">Add Child Node</h2>

            <form action="/structures" method="post">
                @csrf
                <input type="hidden" id="parent_id" name="parent_id" value="">

                <x-input label="Name of the section" name="name" placeholder="IT"/>
                <x-input label="Type" name="type" placeholder="Department"/>

                <label for="child_manager_id" class="block text-sm text-gray-300 mb-2 mt-4">Manager</label>
                <select id="child_manager_id" name="manager_id" class="w-full h-12 px-4 pr-10 bg-gray-900 text-white border border-gray-700 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none transition">
                    <option value="" disabled selected>Select a manager</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" class="bg-gray-800">{{ $user->name}}</option>
                    @endforeach
                </select>

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
    </div>

    <script>
        function renderForm(){
            const overlay1 = document.getElementById('overlay1');
            const rootForm = document.getElementById('root-form');
            const childForm = document.getElementById('child-form');
            const rootNodeButton = document.getElementById('root-node');
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
                hideModal(rootForm);
                hideModal(childForm);
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-child-btn')) {
                    document.getElementById('parent_id').value = e.target.getAttribute('data-parent-id');
                    showModal(childForm);
                }
            });


            if (rootNodeButton) {
                rootNodeButton.addEventListener('click', () => showModal(rootForm));
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
        renderForm();
    </script>
</x-auth-layout>
