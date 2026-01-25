<div id="userMenu"
    class="hidden absolute right-0 mt-2 w-44 rounded-lg
               bg-gray-800 shadow-lg ring-1 ring-black/10 z-50">
    <a href="/profile" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
        Profile
    </a>
    <a href="/dashboard" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
        Dashboard
    </a>

    <div class="border-t border-gray-700"></div>

    <form method="POST" action="/logout">
        @csrf
        @method('DELETE')
        <button
            type="submit"
            class="w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-700"
        >
            Logout
        </button>
    </form>
</div>

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
