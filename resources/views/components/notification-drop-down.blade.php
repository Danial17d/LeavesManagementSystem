@php
    $notifications = \App\Models\Notification::all();
@endphp
@if(! $notifications)
    <div id="notificationDropDown" class="hidden absolute right-10 top-16 mt-2 w-100 rounded-lg
               bg-gray-800 shadow-lg ring-1 ring-black/10 z-50 p-5 text-gray-500">
        @foreach($notifications as $notification)
            <div class="flex justify-between mb-2">
                <h1 class="text-lg text-blue-400">{{$notification->title}}</h1>
                <p class="text-sm">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            <p class="text-sm">{{$notification->body}}</p>
            <div class="border-b-2 border-gray-500 my-6"></div>

        @endforeach
    </div>
@else
    <div id="notificationDropDown" class="hidden absolute right-10 top-16 mt-2 w-100 rounded-lg
               bg-gray-800 shadow-lg ring-1 ring-black/10 z-50 p-5 text-center text-gray-500">
        No Notifications
    </div>
@endif

<script>
    const notificationDropDown = document.getElementById('notificationDropDown');
    const button = document.getElementById('notificationButton');
    button.addEventListener('click',function(){
        notificationDropDown.classList.toggle('hidden');
    });
    document.addEventListener('click', (e) => {
        if (!button.contains(e.target) && !notificationDropDown.contains(e.target)) {
            notificationDropDown.classList.add('hidden');
        }
    });
</script>
