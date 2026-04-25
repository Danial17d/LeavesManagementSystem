<x-auth-layout>
    <div class="max-w-6xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <a href="{{ route('calendar.index', ['month' => $selectedDate->month, 'year' => $selectedDate->year]) }}" class="inline-flex items-center text-sm font-semibold text-blue-300 transition hover:text-blue-200">
                        Back to calendar
                    </a>
                    <h1 class="mt-4 text-3xl font-bold text-white">Leave Requests on {{ $selectedDate->format('F d, Y') }}</h1>
                    <p class="text-slate-400 mt-2">Approved leave requests for your structure on the selected day.</p>
                </div>

                <div class="rounded-lg bg-gray-900/70 border border-gray-700 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Total Requests</p>
                    <p class="text-white font-semibold mt-2">{{ $leaveRequests->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <x-table
                title="Daily Leave Schedule"
                subtitle="Who requested leave and the approved date range."
                :headers="['Employee', 'Department', 'Leave Type', 'From', 'To', 'Days Remaining']"
                :rows="$leaveRequests"
                :paginate="false"
                empty="No approved leave requests were found for this day."
            >
                @foreach ($leaveRequests as $leaveRequest)
                    @php
                        $toDate = \Carbon\Carbon::parse($leaveRequest->to)->startOfDay();
                        $daysLeft = max(0, $selectedDate->startOfDay()->diffInDays($toDate, false));
                    @endphp
                    <tr class="text-sm text-gray-200">
                        <td class="px-6 py-4 text-center font-medium text-white">{{ $leaveRequest->user?->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-center">{{ $leaveRequest->user?->structure?->name ?? 'Not assigned' }}</td>
                        <td class="px-6 py-4 text-center">{{ $leaveRequest->leaveType?->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-center">{{ \Carbon\Carbon::parse($leaveRequest->from)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-center">{{ $toDate->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                {{ $daysLeft === 0 ? 'bg-red-500/20 text-red-300 border border-red-500/40' : 'bg-blue-500/10 text-blue-300 border border-blue-500/30' }}">
                                {{ $daysLeft === 0 ? 'Last day' : $daysLeft . ' ' . \Illuminate\Support\Str::plural('day', $daysLeft) . ' left' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
    </div>
</x-auth-layout>
