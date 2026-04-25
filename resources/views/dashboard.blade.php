<x-auth-layout>
    <x-error/>
    <x-status/>

    @php
        $hour = now('Asia/Riyadh')->hour;
         $greeting = match(true) {
             $hour < 12 => 'Good morning',
             $hour < 17 => 'Good afternoon',
             default => 'Good evening',
         };

         $statusBadge = [
             'submitted' => 'bg-sky-500/15 text-sky-300 border border-sky-500/40',
             'pending' => 'bg-amber-500/15 text-amber-300 border border-amber-500/40',
             'approved' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40',
             'rejected' => 'bg-red-500/15 text-red-300 border border-red-500/40',
             'cancelled' => 'bg-gray-500/15 text-gray-300 border border-gray-500/40',
         ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 py-10 space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-white">{{ $greeting }}, {{ auth()->user()->name }}!</h1>
                <p class="text-slate-400 mt-1 text-sm">{{ now()->format('l, F j, Y') }}</p>
            </div>
            @can(\App\Enums\PermissionType::LeaveRequestCreate)
                @if (! auth()->user()->loadMissing('structure.parent')->isChiefExecutive())
                    <a href="{{ route('leave-requests.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg transition duration-300 hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        New Leave Request
                    </a>
                @endif
            @endcan
        </div>

        @if ($setupChecklist && ! $setupChecklist['allDone'])
            <div class="bg-gray-800 rounded-xl p-8 ring-2 ring-amber-500/40">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-500/20 border border-amber-500/40">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">System Setup Required</h2>
                        <p class="text-slate-400 mt-1 text-sm">Complete the steps below before employees can use the system.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @foreach ($setupChecklist['steps'] as $index => $step)
                        <div class="flex items-start gap-4 rounded-lg border {{ $step['done'] ? 'border-emerald-500/30 bg-emerald-500/5' : 'border-gray-700 bg-gray-900/50' }} p-4">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $step['done'] ? 'bg-emerald-600' : 'bg-gray-700 border border-gray-600' }}">
                                @if ($step['done'])
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                @else
                                    <span class="text-xs font-bold text-slate-400">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm {{ $step['done'] ? 'text-emerald-400 line-through decoration-emerald-600' : 'text-white' }}">
                                    {{ $step['label'] }}
                                </p>
                                @if (! $step['done'])
                                    <p class="text-slate-400 text-xs mt-1">{{ $step['hint'] }}</p>
                                @endif
                            </div>
                            @if (! $step['done'])
                                <a href="{{ $step['route'] }}"
                                   class="shrink-0 inline-flex items-center px-4 py-1.5 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                    Go
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
                <p class="text-slate-500 text-xs mt-5">This checklist is only visible to Super Admins and disappears once all steps are complete.</p>
            </div>
        @endif

        @if ($setupChecklist && $setupChecklist['allDone'])
            <div class="bg-gray-800 rounded-xl p-6 ring-2 ring-emerald-500/30 flex items-center gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-600">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">System Ready</h2>
                    <p class="text-slate-400 text-sm mt-0.5">All setup steps are complete. The system is fully operational.</p>
                </div>
            </div>
        @endif

        {{-- ─── Admin / Super Admin dashboard ───────────────────────────── --}}
        @if ($adminStats)

            {{-- Stat cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Total Employees --}}
                <div class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-2 hover:ring-blue-500/30">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-600/20 border border-blue-500/30">
                        <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $adminStats['totalEmployees'] }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">Total Employees</p>
                    </div>
                </div>

                {{-- Pending Approvals --}}
                <a href="{{ route('leave-approvals.index') }}"
                   class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-2 hover:ring-amber-500/30">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 border border-amber-500/30">
                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $adminStats['pendingApprovals'] }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">Pending Approvals</p>
                    </div>
                </a>

                {{-- Approved This Month --}}
                <div class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-2 hover:ring-emerald-500/30">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600/20 border border-emerald-500/30">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $adminStats['approvedThisMonth'] }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">Approved This Month</p>
                    </div>
                </div>

                {{-- Leave Types --}}
                <a href="{{ route('leave-types.index') }}"
                   class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-2 hover:ring-violet-500/30">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-600/20 border border-violet-500/30">
                        <svg class="w-6 h-6 text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $adminStats['totalLeaveTypes'] }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">Leave Types</p>
                    </div>
                </a>
            </div>

            {{-- Status overview + recent requests --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Status breakdown --}}
                <div class="bg-gray-800 rounded-xl p-6">
                    <h2 class="text-base font-bold text-white mb-5">Request Overview</h2>

                    @php
                        $total = max($adminStats['totalRequests'], 1);
                        $bars  = [
                            ['label' => 'Submitted', 'key' => 'Submitted', 'bar' => 'bg-sky-500',     'text' => 'text-sky-300'],
                            ['label' => 'Pending',   'key' => 'Pending',   'bar' => 'bg-amber-500',   'text' => 'text-amber-300'],
                            ['label' => 'Approved',  'key' => 'Approved',  'bar' => 'bg-emerald-500', 'text' => 'text-emerald-300'],
                            ['label' => 'Rejected',  'key' => 'Rejected',  'bar' => 'bg-red-500',     'text' => 'text-red-300'],
                            ['label' => 'Cancelled', 'key' => 'Cancelled', 'bar' => 'bg-gray-500',    'text' => 'text-gray-300'],
                        ];
                    @endphp

                    <div class="space-y-4">
                        @foreach ($bars as $bar)
                            @php $count = $adminStats['byStatus'][$bar['key']] ?? 0; @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="{{ $bar['text'] }} font-medium">{{ $bar['label'] }}</span>
                                    <span class="text-slate-400 tabular-nums">{{ $count }}</span>
                                </div>
                                <div class="h-1.5 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="{{ $bar['bar'] }} h-full rounded-full transition-all duration-700"
                                         style="width: {{ round(($count / $total) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-700 mt-5 pt-4 flex justify-between text-sm">
                        <span class="text-slate-400">Total Requests</span>
                        <span class="text-white font-bold tabular-nums">{{ $adminStats['totalRequests'] }}</span>
                    </div>
                </div>

                {{-- Recent leave requests --}}
                <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-bold text-white">Recent Leave Requests</h2>
                        @can(\App\Enums\PermissionType::LeaveRequestList)
                            <a href="{{ route('leave-requests.index') }}"
                               class="text-sm text-blue-400 hover:text-blue-300 transition">View all →</a>
                        @endcan
                    </div>

                    @if ($adminStats['recentRequests']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-14 text-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-700 mb-4">
                                <svg class="w-7 h-7 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                </svg>
                            </div>
                            <p class="text-slate-400 text-sm">No leave requests have been submitted yet.</p>
                        </div>
                    @else
                        <div class="space-y-2.5">
                            @foreach ($adminStats['recentRequests'] as $req)
                                <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-900/60 border border-gray-700/60 px-4 py-3 transition hover:border-gray-600">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                                            {{ $req->user?->initials ?? '??' }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-white font-semibold text-sm truncate">{{ $req->user?->name }}</p>
                                            <p class="text-slate-400 text-xs mt-0.5">
                                                {{ $req->leaveType?->name }}
                                                &middot;
                                                {{ \Carbon\Carbon::parse($req->from)->format('M d') }}–{{ \Carbon\Carbon::parse($req->to)->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold {{ $statusBadge[strtolower($req->status)] ?? 'bg-gray-500/15 text-gray-300 border border-gray-500/40' }}">
                                        {{ str($req->status)->headline() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="bg-gray-800 rounded-xl p-6">
                <h2 class="text-base font-bold text-white mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-3">
                    @can(\App\Enums\PermissionType::UserList)
                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-medium hover:bg-gray-600 transition">
                            <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                            </svg>
                            Manage Employees
                        </a>
                    @endcan

                    @can(\App\Enums\PermissionType::LeaveApprovalList)
                        <a href="{{ route('leave-approvals.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 text-sm font-medium hover:bg-amber-500/20 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            Review Approvals
                        </a>
                    @endcan

                    @can(\App\Enums\PermissionType::LeaveRequestList)
                        <a href="{{ route('leave-requests.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-medium hover:bg-gray-600 transition">
                            <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                            </svg>
                            All Requests
                        </a>
                    @endcan

                    @can(\App\Enums\PermissionType::LeaveTypeList)
                        <a href="{{ route('leave-types.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-medium hover:bg-gray-600 transition">
                            <svg class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                            </svg>
                            Leave Types
                        </a>
                    @endcan

                    @can(\App\Enums\PermissionType::StructureList)
                        <a href="{{ route('structures.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-medium hover:bg-gray-600 transition">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                            </svg>
                            Departments
                        </a>
                    @endcan

                    @can(\App\Enums\PermissionType::CalendarView)
                        <a href="{{ route('calendar.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-medium hover:bg-gray-600 transition">
                            <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                            </svg>
                            Calendar
                        </a>
                    @endcan
                </div>
            </div>


            @if ($adminStats['managedStructure'])
                @php $ms = $adminStats['managedStructure']; @endphp
                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600/20 border border-emerald-500/30">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Department You Manage</h2>
                                @if ($ms->parent)
                                    <p class="text-slate-500 text-xs mt-0.5">
                                        <span class="text-slate-400">{{ $ms->parent->name }}</span>
                                        <span class="mx-1 text-slate-600">›</span>
                                        <span class="text-emerald-400 font-medium">{{ $ms->name }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold bg-gray-700 text-slate-300 border border-gray-600">
                                {{ ucfirst($ms->type) }}
                            </span>
                            @can(\App\Enums\PermissionType::StructureView)
                                <a href="{{ route('structures.show', $ms) }}"
                                   class="text-sm text-blue-400 hover:text-blue-300 transition">View →</a>
                            @endcan
                        </div>
                    </div>

                    @if ($ms->users->isEmpty())
                        <p class="text-slate-500 text-sm">No employees assigned to this department yet.</p>
                    @else
                        <div class="flex flex-wrap gap-3">
                            @foreach ($ms->users as $member)
                                <div class="flex items-center gap-2.5 rounded-lg bg-gray-900/60 border border-gray-700/60 px-3 py-2">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                                        {{ $member->initials }}
                                    </div>
                                    <div>
                                        <p class="text-white text-xs font-semibold leading-tight">{{ $member->name }}</p>
                                        <p class="text-slate-500 text-xs">{{ $member->roles->first()?->name ?? 'No role' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-slate-500 text-xs mt-4">{{ $ms->users->count() }} {{ $ms->users->count() == 1 ? 'member' : 'members' }}</p>
                    @endif
                </div>
            @endif

        @endif

        @if ($employeeStats)

            {{-- Stat cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- In-progress requests --}}
                <div class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-2 hover:ring-amber-500/30">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 border border-amber-500/30">
                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $employeeStats['pendingCount'] }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">In Progress</p>
                    </div>
                </div>

                {{-- Days taken this year --}}
                <div class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-2 hover:ring-emerald-500/30">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600/20 border border-emerald-500/30">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $employeeStats['approvedDaysThisYear'] }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">Days Taken — {{ now()->year }}</p>
                    </div>
                </div>

                {{-- Total requests this year --}}
                <div class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:ring-2 hover:ring-blue-500/30">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-600/20 border border-blue-500/30">
                        <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-white">{{ $employeeStats['totalRequestsThisYear'] }}</p>
                        <p class="text-slate-400 text-sm mt-0.5">Requests — {{ now()->year }}</p>
                    </div>
                </div>
            </div>

            {{-- Your department --}}
            @if ($employeeStats['structure'])
                @php $dept = $employeeStats['structure']; @endphp
                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                        {{-- Left: structure info --}}
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-600/20 border border-blue-500/30">
                                <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-base font-bold text-white">Your Department</h2>
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-gray-700 text-slate-300 border border-gray-600">
                                        {{ ucfirst($dept->type) }}
                                    </span>
                                </div>
                                @if ($dept->parent)
                                    <p class="text-slate-500 text-xs mt-1">
                                        <span class="text-slate-400">{{ $dept->parent->name }}</span>
                                        <span class="mx-1 text-slate-600">›</span>
                                        <span class="text-blue-400 font-medium">{{ $dept->name }}</span>
                                    </p>
                                @else
                                    <p class="text-blue-400 text-sm font-medium mt-0.5">{{ $dept->name }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Right: manager info --}}
                        @if ($dept->manager)
                            <div class="flex items-center gap-3 rounded-lg bg-gray-900/60 border border-gray-700/60 px-4 py-3 sm:shrink-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">
                                    {{ $dept->manager->initials }}
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wide">Manager</p>
                                    <p class="text-white text-sm font-semibold leading-tight">{{ $dept->manager->name }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 rounded-lg bg-gray-900/60 border border-gray-700/60 px-4 py-3 sm:shrink-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-700 border border-gray-600">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wide">Manager</p>
                                    <p class="text-slate-400 text-sm">Not assigned</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-gray-800 rounded-xl p-6 flex items-center gap-4 border border-amber-500/30 ring-1 ring-amber-500/20">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-500/20 border border-amber-500/40">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-semibold text-sm">You haven't been assigned to a department yet.</p>
                        <p class="text-slate-400 text-xs mt-0.5">Contact your admin or submit a structure request to get assigned.</p>
                    </div>
                    @can(\App\Enums\PermissionType::StructureRequestCreate)
                        <a href="{{ route('structure-requests.index') }}"
                           class="shrink-0 inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                            Request
                        </a>
                    @endcan
                </div>
            @endif

            {{-- Leave balances + recent requests --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Leave balances --}}
                <div class="bg-gray-800 rounded-xl p-6">
                    <h2 class="text-base font-bold text-white mb-5">Leave Balances — {{ now()->year }}</h2>

                    @if ($employeeStats['leaveBalances']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-700 mb-3">
                                <svg class="w-6 h-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                                </svg>
                            </div>
                            <p class="text-slate-400 text-sm">No balance records for {{ now()->year }}.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($employeeStats['leaveBalances'] as $balance)
                                @php
                                    $total  = max($balance->entitled_days + $balance->carried_days, 1);
                                    $remaining = max(0, $balance->entitled_days + $balance->carried_days - $balance->used_days);
                                    $usedPct = min(100, (int) round(($balance->used_days / $total) * 100));
                                    $barColor  = $usedPct >= 80 ? 'bg-red-500' : ($usedPct >= 50 ? 'bg-amber-500' : 'bg-emerald-500');
                                @endphp
                                <div class="rounded-lg bg-gray-900/60 border border-gray-700/60 p-4">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <span class="font-semibold text-white text-sm">{{ $balance->leaveType?->name }}</span>
                                        <span class="text-xs text-slate-400 tabular-nums">{{ $remaining }} / {{ $total }} days left</span>
                                    </div>
                                    <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                        <div class="{{ $barColor }} h-full rounded-full transition-all duration-700"
                                             style="width: {{ $usedPct }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-slate-500 mt-2">
                                        <span>{{ $balance->used_days }} used</span>
                                        @if ($balance->carried_days > 0)
                                            <span class="text-blue-400">+{{ $balance->carried_days }} carried</span>
                                        @endif
                                        <span>{{ $balance->entitled_days }} entitled</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Recent requests --}}
                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-bold text-white">My Recent Requests</h2>
                        @can(\App\Enums\PermissionType::LeaveRequestList)
                            <a href="{{ route('leave-requests.index') }}"
                               class="text-sm text-blue-400 hover:text-blue-300 transition">View all →</a>
                        @endcan
                    </div>

                    @if ($employeeStats['recentRequests']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-700 mb-3">
                                <svg class="w-6 h-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                </svg>
                            </div>
                            <p class="text-slate-400 text-sm">No leave requests yet.</p>
                            @if (! auth()->user()->loadMissing('structure.parent')->isChiefExecutive())
                                <a href="{{ route('leave-requests.create') }}"
                                   class="mt-3 inline-flex items-center gap-1.5 text-sm text-blue-400 hover:text-blue-300 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                    Create your first request
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="space-y-2.5">
                            @foreach ($employeeStats['recentRequests'] as $req)
                                <a href="{{ route('leave-requests.show', $req) }}"
                                   class="flex items-center justify-between gap-3 rounded-lg bg-gray-900/60 border border-gray-700/60 px-4 py-3 transition hover:border-gray-600 group">
                                    <div class="min-w-0">
                                        <p class="text-white font-semibold text-sm group-hover:text-blue-300 transition truncate">
                                            {{ $req->leaveType?->name }}
                                        </p>
                                        <p class="text-slate-400 text-xs mt-0.5">
                                            {{ \Carbon\Carbon::parse($req->from)->format('M d') }}–{{ \Carbon\Carbon::parse($req->to)->format('M d, Y') }}
                                            &middot;
                                            {{ $req->requested_days }} {{ $req->requested_days == 1 ? 'day' : 'days' }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold {{ $statusBadge[strtolower($req->status)] ?? 'bg-gray-500/15 text-gray-300 border border-gray-500/40' }}">
                                        {{ str($req->status)->headline() }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payroll records --}}
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-600/20 border border-violet-500/30">
                        <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Payroll — {{ now()->year }}</h2>
                        <p class="text-slate-500 text-xs mt-0.5">Your salary records for this year</p>
                    </div>
                </div>

                @if ($employeeStats['payrolls']->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-700 mb-3">
                            <svg class="w-6 h-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                            </svg>
                        </div>
                        <p class="text-slate-400 text-sm">No payroll records for {{ now()->year }} yet.</p>
                    </div>
                @else
                    @php
                        $currentPayroll = $employeeStats['payrolls']->firstWhere('month', now()->month);
                        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        $statusPayrollBadge = [
                            'draft'    => 'bg-gray-500/15 text-gray-300 border border-gray-500/40',
                            'approved' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40',
                            'finalized'=> 'bg-blue-500/15 text-blue-300 border border-blue-500/40',
                        ];
                    @endphp

                    {{-- Current month highlight --}}
                    @if ($currentPayroll)
                        <div class="rounded-xl bg-violet-600/10 border border-violet-500/30 p-5 mb-5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">
                                        {{ $months[$currentPayroll->month - 1] }} {{ $currentPayroll->year }} &mdash; Current Month
                                    </p>
                                    <p class="text-3xl font-bold text-white tabular-nums">
                                        {{ number_format($currentPayroll->net_salary, 2) }}
                                        <span class="text-base font-normal text-slate-400">net</span>
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-4 sm:text-right">
                                    <div>
                                        <p class="text-xs text-slate-500 uppercase tracking-wide">Basic</p>
                                        <p class="text-white font-semibold tabular-nums">{{ number_format($currentPayroll->basic_salary, 2) }}</p>
                                    </div>
                                    @if ($currentPayroll->unpaid_deduction > 0)
                                        <div>
                                            <p class="text-xs text-slate-500 uppercase tracking-wide">Deduction</p>
                                            <p class="text-red-400 font-semibold tabular-nums">-{{ number_format($currentPayroll->unpaid_deduction, 2) }}</p>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-xs text-slate-500 uppercase tracking-wide">Status</p>
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $statusPayrollBadge[$currentPayroll->status] ?? 'bg-gray-500/15 text-gray-300 border border-gray-500/40' }}">
                                            {{ ucfirst($currentPayroll->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- All months table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left text-xs uppercase tracking-wide text-slate-500 pb-3 pr-4">Month</th>
                                    <th class="text-right text-xs uppercase tracking-wide text-slate-500 pb-3 px-4">Basic Salary</th>
                                    <th class="text-right text-xs uppercase tracking-wide text-slate-500 pb-3 px-4">Deduction</th>
                                    <th class="text-right text-xs uppercase tracking-wide text-slate-500 pb-3 px-4">Net Salary</th>
                                    <th class="text-right text-xs uppercase tracking-wide text-slate-500 pb-3 pl-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/60">
                                @foreach ($employeeStats['payrolls'] as $pr)
                                    @php $isCurrentMonth = $pr->month == now()->month; @endphp
                                    <tr class="{{ $isCurrentMonth ? 'bg-violet-500/5' : '' }} transition hover:bg-gray-700/20">
                                        <td class="py-3 pr-4 font-medium {{ $isCurrentMonth ? 'text-violet-300' : 'text-white' }}">
                                            {{ $months[$pr->month - 1] }} {{ $pr->year }}
                                            @if ($isCurrentMonth)
                                                <span class="ml-1.5 text-xs text-violet-400">(current)</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right text-slate-300 tabular-nums">
                                            {{ number_format($pr->basic_salary, 2) }}
                                        </td>
                                        <td class="py-3 px-4 text-right tabular-nums {{ $pr->unpaid_deduction > 0 ? 'text-red-400' : 'text-slate-500' }}">
                                            {{ $pr->unpaid_deduction > 0 ? '-'.number_format($pr->unpaid_deduction, 2) : '—' }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-semibold text-white tabular-nums">
                                            {{ number_format($pr->net_salary, 2) }}
                                        </td>
                                        <td class="py-3 pl-4 text-right">
                                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $statusPayrollBadge[$pr->status] ?? 'bg-gray-500/15 text-gray-300 border border-gray-500/40' }}">
                                                {{ ucfirst($pr->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        @endif

        {{-- Fallback: no role-matched content --}}
        @if (! $adminStats && ! $employeeStats)
            <div class="bg-gray-800 rounded-xl p-10 text-center ring-2 ring-gray-700">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-700 mx-auto mb-4">
                    <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">Welcome to LMS</h2>
                <p class="text-slate-400 text-sm max-w-sm mx-auto">
                    Your account hasn't been assigned a role yet. Please contact your administrator to get set up.
                </p>
            </div>
        @endif

    </div>
</x-auth-layout>
