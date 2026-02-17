<x-guest-layout>
    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white text-center">Verify Your Email</h1>
            <p class="text-gray-300 text-center mt-2">
                We sent a verification link to your email. Please verify your account to continue.
            </p>

            @if (session('status'))
                <div class="mt-6 mb-2 rounded-lg bg-green-100 px-4 py-3 text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-8">
                @csrf
                <x-button class="w-full">Resend Verification Email</x-button>
            </form>
        </div>
    </div>
</x-guest-layout>
