<x-auth-layout>
    <div class="max-w-6xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10 text-blue-300 border border-blue-500/30">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">Leave Calendar</h1>
                            <p class="text-slate-400 mt-1">Static monthly view aligned with the LMS dashboard style.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-gray-900/70 border border-gray-700 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Viewing</p>
                    <p class="text-white font-semibold mt-2">{{ $calendarDate->format('F Y') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $calendarDate->format('F Y') }}</h2>
                    <p class="text-slate-400 mt-2">Use the month controls to browse the static leave calendar.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('calendar.index', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-900 border border-gray-700 text-white font-semibold transition duration-300 ease-in-out hover:bg-gray-700"
                    >
                        Previous
                    </a>
                    <a
                        href="{{ route('calendar.index', ['month' => $today->month, 'year' => $today->year]) }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold transition duration-300 ease-in-out hover:bg-blue-700"
                    >
                        Today
                    </a>
                    <a
                        href="{{ route('calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-900 border border-gray-700 text-white font-semibold transition duration-300 ease-in-out hover:bg-gray-700"
                    >
                        Next
                    </a>
                </div>
            </div>

            <x-divider/>

            <div class="overflow-hidden rounded-lg border border-gray-700 bg-gray-900/60">
                <div class="grid grid-cols-7 border-b border-gray-700 bg-gray-800/80">
                    @foreach ($weekDays as $weekDay)
                        <div class="px-3 py-4 text-center text-xs font-semibold uppercase tracking-wide text-slate-300">
                            {{ $weekDay }}
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7">
                    @foreach ($cells as $day)
                        @php
                            $isToday = $day
                                && $today->day === $day
                                && $today->month === $calendarDate->month
                                && $today->year === $calendarDate->year;
                            $leaveCount = $day ? ($leaveEvent[$day] ?? 0) : 0;
                        @endphp

                        <div class="min-h-28 border-b border-r border-gray-700 p-3 {{ $day ? 'bg-gray-900/40 hover:bg-gray-800/70' : 'bg-gray-900/20' }} transition duration-300 ease-in-out">
                            @if ($day)
                                <div class="flex items-start justify-between">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold {{ $isToday ? 'bg-blue-600 text-white' : 'text-white' }}">
                                        {{ $day }}
                                    </span>
                                    @if ($isToday)
                                        <span class="rounded-full bg-blue-500/10 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-blue-200 border border-blue-500/30">
                                            Today
                                        </span>
                                    @endif
                                </div>

                                @if ($leaveCount > 0)
                                    <a href="{{ route('calendar.show', ['year' => $calendarDate->year, 'month' => $calendarDate->month, 'day' => $day]) }}">
                                        <div class="mt-6 space-y-2">
                                            <div class="rounded-md border border-blue-600/40 bg-blue-700 px-2 py-2 text-xs text-white">
                                                {{ $leaveCount }} {{ \Illuminate\Support\Str::plural('leave request', $leaveCount) }}
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <div class="mt-6 space-y-2">
                                        <div class="rounded-md border border-dashed border-gray-700 px-2 py-2 text-xs text-slate-400">
                                            No leave events
                                        </div>
                                    </div>
                                @endif

                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
