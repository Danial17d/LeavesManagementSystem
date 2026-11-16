@php
    $features = [
        [
            'title' => 'Easy Leave Requests',
            'text'  => 'Submit leave requests quickly with a simple and clear process.',
        ],
        [
            'title' => 'Transparent Approvals',
            'text'  => 'Know exactly where your request stands at every step.',
        ],
        [
            'title' => 'Real-Time Tracking',
            'text'  => 'Track approval status without emails or paperwork.',
        ],
        [
            'title' => 'Secure Access',
            'text'  => 'Role-based permissions and email-verified accounts.',
        ],
    ];
@endphp

<x-guest-layout title="Welcome">
    <x-error/>
    <div class="max-w-4xl mx-auto mt-10 mb-10 px-4">
        <div class="bg-gray-800 text-white p-6 rounded-lg text-center">
            <h1 class="text-2xl font-bold">
                @auth
                    Welcome back, {{ auth()->user()->name }}
                @else
                    Welcome to the Leave Management System
                @endauth
            </h1>
            <p class="mt-2 text-gray-300">
                The best place to track your request approvals
            </p>
        </div>

        <div class="bg-gray-800 mt-4 rounded-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">

                @foreach ($features as $feature)
                    <div class="bg-gray-700 rounded-lg p-6 text-center">
                        <h2 class="text-xl font-semibold text-white">
                            {{ $feature['title'] }}
                        </h2>
                        <p class="text-gray-300 mt-2">
                            {{ $feature['text'] }}
                        </p>
                    </div>
                @endforeach

                <div class="col-span-full p-5 flex justify-center">
                    @auth
                        <x-button type="link" href="{{ route('dashboard') }}">
                            Go to Dashboard
                        </x-button>
                    @else
                        <x-button type="link" href="{{ route('register') }}">
                            Get Started
                        </x-button>
                    @endauth
                </div>

            </div>
        </div>

    </div>
</x-guest-layout>
