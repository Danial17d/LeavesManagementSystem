<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col">

<header class="flex justify-between items-center p-4 bg-gray-800">
    <div class="text-4xl font-bold">
        LMS
    </div>

    @guest
        <div>
            <x-button href="/login" type="link">
                Login
            </x-button>
        </div>
    @endguest

    @auth
        <div class="relative inline-block">
            <button
                id="userMenuButton"
                type="button"
                class="w-10 h-10 rounded-full bg-blue-600 hover:bg-blue-700
               flex items-center justify-center
               text-white font-semibold focus:outline-none">
                {{ auth()->user()->getInitialsAttribute() }}
            </button>
        <x-user-menu/>
    @endauth
</header>


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
