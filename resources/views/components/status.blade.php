<div id="status" class="hidden top-19 left-5 absolute opacity-0 translate-y-2
            bg-green-600 text-white rounded-lg p-4 mb-4
            transition-all duration-300 ease-in-out">
    <p class="text-white font-bold">{{session('status')}}</p>
</div>
<script>
    function status() {
        const session = @json(session('status'));
        const status = document.getElementById('status');

        if(session){
            status.classList.remove('hidden');
            setTimeout(() => {
                status.classList.add('opacity-100');
                status.classList.remove('translate-y-2');
            }, 10);

            setTimeout(() => {
                status.classList.remove('opacity-100');
                status.classList.add('-translate-y-2');
                setTimeout(() => {
                    status.classList.add('hidden');
                    status.classList.remove('-translate-y-2');
                    status.classList.add('translate-y-2');
                }, 300);
            }, 3000);
        }
    }
    status();
</script>
