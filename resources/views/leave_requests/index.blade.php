<x-auth-layout>
    <x-error/>
    <x-status/>

    @php
        $statusClasses = [
            'submitted' => 'bg-sky-600 text-white',
            'pending' => 'bg-amber-500 text-gray-950',
            'waiting' => 'bg-slate-600 text-white',
            'approved' => 'bg-emerald-600 text-white',
            'rejected' => 'bg-red-600 text-white',
            'cancelled' => 'bg-gray-500 text-white',
        ];
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-16">
            <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Leave Request Process</h1>
                    <p class="text-slate-400 mt-2">Track every leave request from your submission to the final approver.</p>
                </div>
              <div class="space-x-2">
                  @if (! auth()->user()->loadMissing('structure.parent')->isChiefExecutive())
                      <a href="{{ route('leave-requests.create') }}"
                         class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                          Create Leave Request
                      </a>
                  @endif

                      <a href="{{ route('structure-requests.index') }}"
                         class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                          Structure Requests
                      </a>

              </div>
            </div>
        </div>

        @if (auth()->user()->loadMissing('structure.parent')->isChiefExecutive())
            <div class="bg-amber-500/10 border border-amber-500/40 rounded-lg p-6 mt-8">
                <p class="text-amber-200 font-semibold">The CEO cannot request leave because no higher approver is assigned.</p>
            </div>
        @endif

        @forelse ($leaveRequests as $leaveRequest)
            <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-bold text-white">{{ $leaveRequest->leaveType?->name ?? 'Leave Request' }}</h2>
                            <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold {{ $statusClasses[$leaveRequest->status] ?? 'bg-slate-600 text-white' }}">
                                {{ str($leaveRequest->status)->headline() }}
                            </span>
                        </div>

                        <p class="text-slate-400 mt-2">
                            {{ \Carbon\Carbon::parse($leaveRequest->from)->format('M d, Y') }}
                            to
                            {{ \Carbon\Carbon::parse($leaveRequest->to)->format('M d, Y') }}
                        </p>

                        <div class="mt-4">
                            <a
                                href="{{ route('leave-requests.show', $leaveRequest) }}"
                                class="inline-flex items-center rounded-lg border border-blue-500/40 bg-blue-500/10 px-4 py-2 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/20"
                            >
                                View Details
                            </a>
                        </div>

                        @if ($leaveRequest->reason)
                            <p class="text-gray-300 mt-4 max-w-3xl">{{ $leaveRequest->reason }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-full xl:min-w-[360px] xl:max-w-md">
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Requested By</p>
                            <p class="text-white font-semibold mt-2">{{ $leaveRequest->user?->name }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Current Step</p>
                            <p class="text-white font-semibold mt-2">Step {{ $leaveRequest->current_step }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Approvals</p>
                            <p class="text-white font-semibold mt-2">{{ $leaveRequest->processSteps->count() - 1 }} Levels</p>
                        </div>
                    </div>
                </div>

                <x-divider/>

                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Approval Flow</h3>

                    <div class="space-y-4">
                        @foreach ($leaveRequest->processSteps as $step)
                            @php
                                $badgeClass = $statusClasses[$step['status']] ?? 'bg-slate-600 text-white';
                                $cardClass = $step['is_current']
                                    ? 'border-blue-500/60 ring-2 ring-blue-500/20'
                                    : 'border-gray-700';
                            @endphp

                            <div class="rounded-lg border {{ $cardClass }} bg-gray-900/60 p-5">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                            {{ $step['step'] + 1 }}
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center gap-3">
                                                <h4 class="text-lg font-semibold text-white">{{ $step['title'] }}</h4>
                                                <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                                                    {{ str($step['status'])->headline() }}
                                                </span>
                                                @if ($step['is_current'])
                                                    <span class="inline-flex items-center rounded-lg bg-blue-600/20 px-3 py-1 text-xs font-semibold text-blue-300 border border-blue-500/40">
                                                        Current Step
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-gray-200 mt-2">{{ $step['actor'] }}</p>
                                            <p class="text-slate-400 text-sm mt-1">{{ $step['role'] }}</p>

                                            @if ($step['note'])
                                                <p class="text-slate-300 text-sm mt-3">{{ $step['note'] }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-sm text-slate-400 lg:text-right">
                                        @if ($step['acted_at'])
                                            <p>{{ \Carbon\Carbon::parse($step['acted_at'])->format('M d, Y h:i A') }}</p>
                                        @elseif ($step['status'] === 'waiting')
                                            <p>Waiting for previous approvals</p>
                                        @elseif ($step['status'] === 'pending')
                                            <p>Waiting for action</p>
                                        @else
                                            <p>Created {{ \Carbon\Carbon::parse($leaveRequest->created_at)->format('M d, Y h:i A') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                <h2 class="text-2xl font-bold text-white">No Leave Requests Yet</h2>
                <p class="text-slate-400 mt-2">Create your first leave request to see the full approval process from your account to the final manager.</p>
                @if (! auth()->user()->loadMissing('structure.parent')->isChiefExecutive())
                    <div class="pt-6">
                        <a href="{{ route('leave-requests.create') }}"
                           class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                            Request Leave
                        </a>
                    </div>
                @endif
            </div>
        @endforelse

        @if ($leaveRequests->hasPages())
            <div class="mt-10">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>
</x-auth-layout>
