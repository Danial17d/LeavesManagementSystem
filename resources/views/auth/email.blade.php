<x-layout1>
    <div class="max-w-md mx-auto px-4 py-16">

        <div class="bg-gray-800 rounded-lg p-8
                    transition duration-300 ease-in-out
                    hover:-translate-y-1 hover:shadow-lg
                    hover:ring-2 hover:ring-blue-500/40">

            <h1 class="text-3xl font-bold text-white text-center">Enter Your Email</h1>
            <p class="text-gray-300 text-center mt-2">Forgot Your Password ?</p>
            <form class="mt-8 space-y-5" method="POST" action="/forgot-password">
                @csrf
                <x-input
                    label="Email"
                    name="email"
                    type="email"
                    autofocus
                    placeholder="you@example.com"
                />
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                        {{ session('status') }}
                    </div>
                @endif
                <x-button class="w-full">Send</x-button>

            </form>
        </div>

    </div>
</x-layout1>
