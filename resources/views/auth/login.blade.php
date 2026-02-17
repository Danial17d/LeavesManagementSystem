<x-guest-layout>
    <div class="max-w-md mx-auto px-4 py-16">

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <h1 class="text-3xl font-bold text-white text-center">Login</h1>
            <p class="text-gray-300 text-center mt-2">Welcome back. Please sign in.</p>

            @if(session('status'))
                <div class="mt-6 bg-emerald-900/40 border border-emerald-700 text-emerald-200 rounded-lg p-3">
                    {{ session('status') }}
                </div>
            @endif

            <form class="mt-8 space-y-5" method="POST" action="/login">
                @csrf

                <x-input
                    label="Email"
                    name="email"
                    type="email"
                    autofocus
                    placeholder="you@example.com"
                />

                <x-input
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="••••••••"
                />

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-300">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-gray-700 bg-gray-900
                                   text-blue-500 focus:ring-blue-400 focus:ring-offset-gray-800"
                        >
                        Remember me
                    </label>

                    <a href="/forgot-password" class="text-sm text-gray-300 hover:text-white transition">
                        Forgot password?
                    </a>
                </div>

                <div class="pt-2 flex justify-center">
                    <x-button type="submit" class="w-full">
                        Login
                    </x-button>
                </div>

                <p class="text-sm text-gray-300 text-center mt-4">
                    Don’t have an account?
                    <a href="/register" class="text-blue-400 hover:text-blue-300 transition">Register</a>
                </p>
            </form>
        </div>

    </div>
</x-guest-layout>
