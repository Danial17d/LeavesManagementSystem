@props(['notifications'])
<div id="notificationDropDown" class="hidden absolute right-10 top-16 mt-2 w-44 rounded-lg
               bg-gray-800 shadow-lg ring-1 ring-black/10 z-50 p-5">

</div>
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
