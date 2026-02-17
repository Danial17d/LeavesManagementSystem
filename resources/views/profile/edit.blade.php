<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">My Profile</h1>
            <p class="text-slate-400 mb-8">Update your personal information and password.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Department</p>
                    <p class="text-white font-semibold">{{ $user->structure?->name ?? 'Not assigned' }}</p>
                </div>
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-4">
                    <p class="text-gray-400 text-sm mb-1">Leave Balance</p>
                    <p class="text-white font-semibold">{{ $user->balance }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <x-input label="Name" name="name" :value="$user->name"/>
                <x-input label="Email" name="email" type="email" :value="$user->email"/>
                <x-input label="New Password" name="password" type="password" autocomplete="new-password"/>
                <x-input label="Confirm New Password" name="password_confirmation" type="password" autocomplete="new-password"/>

                <div class="pt-4 flex gap-3">
                    <x-button>Save changes</x-button>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
