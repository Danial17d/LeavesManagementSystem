<x-auth-layout>
    <div class="max-w-3xl mx-auto px-4 py-16">
        <div
            class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">Request for a leave</h1>
            <p class="text-slate-400 mb-8">Please complete the form below to submit your leave request for approval.</p>
            @if($isThereTeamLeaveRequest)
                <div class="flex items-center gap-3 bg-amber-950 border border-amber-700 border-l-4 border-l-amber-400 px-4 py-3">
                    <svg class="w-5 h-5 shrink-0 text-amber-400" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2L2 20h20L12 2z" fill="currentColor" opacity="0.2"/>
                        <path d="M12 2L2 20h20L12 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <line x1="12" y1="9" x2="12" y2="14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="12" cy="17" r="0.5" fill="currentColor" stroke="currentColor" stroke-width="1"/>
                    </svg>
                    <p class="text-sm text-amber-200 leading-relaxed">
                        There is a teammate who is on leave right now —
                        <a href="{{ route('calendar.index') }}" class="text-amber-400 underline font-medium hover:text-amber-300">
                            check the calendar
                        </a>
                    </p>
                </div>
            @endif


            <form class="space-y-4" action="{{route('leave-requests.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input label="Employee Name" name="employee_name" value="{{auth()->user()->name}}" disabled/>
                    <x-input label="Balance" name="balance" type="number" min="0" value="" readonly />
                </div>

                <div id="leave-balances-data"
                     data-balances='@json($leaveBalances)'
                     class="hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="leave_type" class="block text-sm text-gray-300 mb-2 mt-1">Leave Type</label>
                        <select id="leave_type" name="leave_type"
                                class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 h-12 outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                            <option value="" {{ old('leave_type') ? '' : 'selected' }}>No value selected</option>
                            @foreach($leaveTypes as $leaveType)
                                <option id="leave-type" value="{{$leaveType->name}}" {{ old('leave_type') === $leaveType->name ? 'selected' : '' }}>{{$leaveType->name}}-({{$leaveType->days}}) </option>
                            @endforeach
                        </select>
                        @error('leave_type')
                        <p class="text-red-400 text-sm mt-2 mb-2">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <div>
                            <label for="days-requested"  class="block text-sm text-gray-300 mb-2 mt-1">Days
                                Requested</label>
                            <input id="requested-day" readonly name="days_requested" type="number" min="1" value="{{ old('days_requested', 1) }}"
                                   class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 h-12 outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                            @error('days_requested')
                            <p class="text-red-400 text-sm mt-2 mb-2">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:col-span-2">
                        <div>
                            <label for="start-date" class="block text-sm text-gray-300 mb-2 mt-1">Start Date</label>
                            <input id="start-date" name="start_date" type="date" value="{{ old('start_date') }}"
                                   class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 h-12 outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                            @error('start_date')
                            <p class="text-red-400 text-sm mt-2 mb-2">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label for="end-date" class="block text-sm text-gray-300 mb-2 mt-1">End Date</label>
                            <input id="end-date" name="end_date" type="date" value="{{ old('end_date') }}"
                                   class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 h-12 outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                            @error('end_date')
                            <p class="text-red-400 text-sm mt-2 mb-2">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm text-gray-300 mb-2 mt-1">Reason</label>
                    <textarea id="reason" name="reason" rows="4"
                              placeholder="Write a short reason for your leave request."
                              class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">{{ old('reason') }}</textarea>
                    @error('reason')
                    <p class="text-red-400 text-sm mt-2 mb-2">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <x-input label="Attachment" name="attachment" type="file"/>
                @error('attachment')
                <p class="text-red-400 text-sm mt-2 mb-2">
                    {{ $message }}
                </p>
                @enderror
                @error('attachment.*')
                <p class="text-red-400 text-sm mt-2 mb-2">
                    {{ $message }}
                </p>
                @enderror
                <div id="file-list" class="space-y-1 text-sm text-gray-300">

                </div>
                <div class="pt-4 flex gap-3">
                    <button type="submit"
                            class="inline-flex w-full items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Submit Request
                    </button>
                    <a type="button" href="{{route('leave-requests.index')}}"
                            class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script>
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const requestedDaysInput = document.getElementById('requested-day');
        const balanceInput = document.getElementById('balance');
        const leaveTypeSelect = document.getElementById('leave_type');
        const input = document.querySelector('input[name="attachment"]');
        const fileList = document.getElementById('file-list');
        const leaveBalance = document.getElementById('leave-balances-data');
        const balances = JSON.parse(leaveBalance.dataset.balances);
        updateBalance();





        if (input && fileList) {
            input.addEventListener('change', function () {
                fileList.innerHTML = '';

                if (!this.files.length) {
                    fileList.textContent = 'No file selected';
                    return;
                }

                for (let i = 0; i < this.files.length; i++) {
                    const fileName = document.createElement('p');
                    fileName.textContent = this.files[i].name;
                    fileList.appendChild(fileName);
                }
            });
        }
        function updateBalance() {
            const selectedType = leaveTypeSelect.value;
            balanceInput.value = selectedType && Object.prototype.hasOwnProperty.call(balances, selectedType)
                ? balances[selectedType]
                : '';
        }
        function updateRequestedDays() {
            const startValue = startDateInput.value;
            const endValue = endDateInput.value;

            if (!startValue || !endValue) {
                requestedDaysInput.value = 1;
                return;
            }

            const start = new Date(startValue + 'T00:00:00');
            const end = new Date(endValue + 'T00:00:00');

            if (end < start) {
                requestedDaysInput.value = 0;
                return;
            }

            const msPerDay = 24 * 60 * 60 * 1000;
            const diffInDays = Math.floor((end - start) / msPerDay) + 1;
            requestedDaysInput.value = diffInDays;
        }

        startDateInput.addEventListener('change', () => {
            endDateInput.min = startDateInput.value || '';
            updateRequestedDays();
        });

        leaveTypeSelect.addEventListener('change', updateBalance);
        endDateInput.addEventListener('change', updateRequestedDays);
    </script>
</x-auth-layout>
