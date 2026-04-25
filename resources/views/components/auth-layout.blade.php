<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col">

<header class="flex justify-between items-center p-4 bg-gray-800">
    <div class="text-4xl font-bold text-white">
        LMS
    </div>
    @auth
        @php
            $hasUnreadNotifications = auth()->user()
                ->notifications()
                ->where('read', false)
                ->exists();
        @endphp
        <div class="space-x-10">
            <button id="notificationButton" class="text-white hover:text-gray-300">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" fill="" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"  stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle
                        id="notificationUnreadIndicator"
                        cx="18"
                        cy="6"
                        r="3"
                        fill="#FF4757"
                        stroke="#fff"
                        stroke-width="1.5"
                        class="{{ $hasUnreadNotifications ? '' : 'hidden' }}"
                    />

                </svg>
            </button>
            <button id="menuBtn" class="text-white hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="3" x2="9" y2="21"/>
                </svg>
            </button>
        </div>
    @endauth
</header>
@auth
    <x-side-bar></x-side-bar>

    <x-notification-drop-down/>
@endauth

<main class="flex-grow">
    {{ $slot }}
</main>


<footer class="bg-gray-800 border-t border-gray-700">
    <div class="max-w-6xl mx-auto px-4 py-6 flex flex-col sm:flex-row justify-between items-center gap-4">

        <div class="text-gray-400 text-sm">
            © {{ date('Y') }} Leave Management System. All rights reserved.
        </div>

        <div class="flex gap-6 text-sm">
            <a href="/about" class="text-gray-400 hover:text-white transition">
                About
            </a>
            <a href="/register" class="text-gray-400 hover:text-white transition">
                Get Started
            </a>
        </div>

    </div>
</footer>

</body>
</html>
