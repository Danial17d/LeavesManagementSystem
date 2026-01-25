<x-layout1>
    <div class="max-w-md mx-auto px-4 py-16">

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <h1 class="text-3xl font-bold text-white text-center">
                Reset Your Password
            </h1>


            <form class="mt-8 space-y-5" method="POST" action="/reset-password">
                @csrf
                <input type="hidden" name="token" value="{{$token}}">
                <input type="hidden" name="email" value="{{$email}}">

                <x-input
                    label="Reset Password"
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
                <x-button class="w-full">
                    Reset
                </x-button>
            </form>
        </div>

    </div>
</x-layout1>

