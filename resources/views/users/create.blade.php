<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">Create User</h1>
            <p class="text-slate-400 mb-8">Add a new user account and assign one or more roles.</p>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf

                <x-input label="Name" name="name" :value="old('name')"/>
                <x-input label="Email" name="email" type="email" :value="old('email')"/>
                <x-input label="Password" name="password" type="password"/>
                <x-input label="Confirm Password" name="password_confirmation" type="password"/>
                <x-input label="Leave Balance" name="balance" type="number" min="0" :value="21"/>
                <x-input label="Salary" name="salary" type="number" min="0" step="0.01" :value="0"/>

                <div>
                    <p class="block text-sm text-gray-300 mb-2 mt-4">Roles</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($roles as $role)
                            <label class="inline-flex items-center gap-2 text-gray-200">
                                <input
                                    type="checkbox"
                                    name="roles[]"
                                    value="{{ $role->name }}"
                                    @checked(in_array($role->name, old('roles', [])))
                                    class="rounded border-gray-600 bg-gray-900 text-blue-600 focus:ring-blue-500"
                                >
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="text-red-400 text-sm mt-2 mb-2">{{ $message }}</p>
                    @enderror
                    @error('roles.*')
                        <p class="text-red-400 text-sm mt-2 mb-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 flex gap-3">
                    <x-button>Create user</x-button>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
