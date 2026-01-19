<x-layout1>
    <div class="max-w-md mx-auto px-4 py-16">

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <h1 class="text-3xl font-bold text-white text-center">Create Account</h1>
            <p class="text-gray-300 text-center mt-2">Get started in a few seconds.</p>

            <form class="mt-8 space-y-5" method="POST" action="/register">
                @csrf

                <x-input
                    label="Full Name"
                    name="name"
                    placeholder="Your name"
                />

                <x-input
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="you@example.com"
                />

                <x-input
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="••••••••"
                />

                <x-input
                    label="Confirm Password"
                    name="password_confirmation"
                    type="password"
                    placeholder="••••••••"
                />

                <div class="pt-2 flex justify-center">
                    <x-button type="submit" class="w-full">
                        Register
                    </x-button>
                </div>

                <p class="text-sm text-gray-300 text-center mt-4">
                    Already have an account?
                    <a href="/login" class="text-blue-400 hover:text-blue-300 transition">Login</a>
                </p>
            </form>
        </div>

    </div>
</x-layout1>
