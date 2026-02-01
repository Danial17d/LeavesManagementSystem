<div id="error"
     class="hidden top-19 left-5 absolute opacity-0 translate-y-2
            bg-red-600 text-white rounded-lg p-4 mb-4
            transition-all duration-300 ease-in-out">
    <ul id="error-list" class="list-none pl-5 space-y-1"></ul>
</div>
<script>
    function showError() {
        const errors = @json($errors->all());
        const errorDiv = document.getElementById('error');
        const errorList = document.getElementById('error-list');


        if (!errors || errors.length === 0) return;

        errorList.innerHTML = '';

        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });

        errorDiv.classList.remove('hidden');

        setTimeout(() => {
            errorDiv.classList.remove('opacity-0', 'translate-y-2');
            errorDiv.classList.add('opacity-100');
        }, 10);

        setTimeout(() => {
            errorDiv.classList.remove('opacity-100');
            errorDiv.classList.add('opacity-0', '-translate-y-2');

            setTimeout(() => {
                errorDiv.classList.add('hidden');
                errorDiv.classList.remove('-translate-y-2');
                errorDiv.classList.add('translate-y-2');
            }, 400);

        }, 4000);
    }
    showError();

</script>
