<div id="overlay" class="fixed inset-0 bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>


<div id="sidebar"
     class="fixed top-0 right-0 h-full w-80 bg-gray-800 text-white z-50 p-6 transform translate-x-full transition-transform duration-300 ease-in-out shadow-2xl">

    <button id="closeBtn" class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300 transition-colors">
        &times;
    </button>


    <div class="flex flex-col items-center mb-8 pt-8">
        <div
            class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-2xl mb-4">
            {{ auth()->user()->getInitialsAttribute() }}
        </div>
        <h3 class="text-xl font-semibold">{{ auth()->user()->name }}</h3>
        <p class="text-gray-400 text-sm">{{ auth()->user()->email }}</p>
    </div>

    <div class="border-t border-gray-700 mb-6"></div>


    <ul class="space-y-2">
        <li>
            <a href="/profile" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                Profile
            </a>
        </li>
        <li>
            <a href="/dashboard" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                Dashboard
            </a>
        </li>
        @can([\App\Enums\PermissionType::UserList,\App\Enums\PermissionType::StructureList])
            <li>
                <a href="/users" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                    Employees
                </a>
            </li>
            <li>
                <a href="/structures" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                    Departments and sections
                </a>
            </li>
        @endcan
        @can(\App\Enums\PermissionType::LeaveTypeList)
            <li>
                <a href="{{ route('leave-types.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                    Leave types
                </a>
            </li>
        @endcan
        @can(\App\Enums\PermissionType::LeaveRequestList)
            <li>
                <a href="{{ route('leave-requests.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                    Requests
                </a>
            </li>
        @endcan
        @can(\App\Enums\PermissionType::LeaveApprovalList)
            <li>
                <a href="{{ route('leave-approvals.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                    Leave Approvals
                </a>
            </li>
        @endcan
        @can(\App\Enums\PermissionType::CalendarView)
            <li>
                <a href="{{ route('calendar.index') }}" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors text-lg">
                    Calendar
                </a>
            </li>
        @endcan


    </ul>

    <div class="border-t border-gray-700 my-6"></div>

    <form method="POST" action="/logout">
        @csrf
        @method('DELETE')
        <x-button
            type="submit"
            class="bg-red-500 w-full text-left px-4 py-3 rounded-lg hover:bg-red-600 transition-colors text-lg text-white hover:text-white"
        >
            Logout
        </x-button>
    </form>
</div>


<script>
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    const closeBtn = document.getElementById('closeBtn');
    const overlay = document.getElementById('overlay');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
    });


    function closeSidebar() {
        sidebar.classList.add('translate-x-full');
        overlay.classList.add('hidden');
    }

    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);


    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !sidebar.classList.contains('translate-x-full')) {
            closeSidebar();
        }
    });
</script>
