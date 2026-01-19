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


            <div
                id="userMenu"
                class="hidden absolute right-0 mt-2 w-44 rounded-lg
               bg-gray-800 shadow-lg ring-1 ring-black/10 z-50"
            >
                <a href="/profile" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                    Profile
                </a>
                <a href="/Dashboard" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                    Dashboard
                </a>

                <div class="border-t border-gray-700"></div>

                <form method="POST" action="/logout">
                    @csrf
                    <button
                        type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </div>

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
<script>
    const button = document.getElementById('userMenuButton');
    const menu = document.getElementById('userMenu');

    button.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!button.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
</script>
