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
        $canCancel = in_array($leaveRequest->status, [\App\Enums\RequestStatus::Pending->value, \App\Enums\RequestStatus::Submitted->value], true);
        $canRevoke = $leaveRequest->status === \App\Enums\RequestStatus::Approved->value
            && \Carbon\Carbon::parse($leaveRequest->to)->isFuture();
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <a href="{{ route('leave-requests.index') }}" class="inline-flex items-center text-sm font-semibold text-blue-300 transition hover:text-blue-200">
                        Back to requests
                    </a>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-bold text-white">{{ $leaveRequest->leaveType?->name ?? 'Leave Request' }}</h1>
                        <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold {{ $statusClasses[$leaveRequest->status] ?? 'bg-slate-600 text-white' }}">
                            {{ str($leaveRequest->status)->headline() }}
                        </span>
                    </div>
                    <p class="text-slate-400 mt-2">
                        {{ \Carbon\Carbon::parse($leaveRequest->from)->format('M d, Y') }}
                        to
                        {{ \Carbon\Carbon::parse($leaveRequest->to)->format('M d, Y') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if ($leaveRequest->attachment)
                        <a
                            href="{{ route('leave-requests.attachment', $leaveRequest) }}"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-blue-500/40 bg-blue-500/10 text-blue-200 font-semibold transition hover:bg-blue-500/20"
                        >
                            Download Attachment
                        </a>
                    @endif

                    @if ($canCancel)
                        <form method="POST" action="{{ route('leave-requests.cancel', $leaveRequest) }}">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-red-600 text-white font-semibold transition hover:bg-red-700"
                            >
                                Cancel Request
                            </button>
                        </form>
                    @endif

                    @can(\App\Enums\PermissionType::LeaveRequestRevoke)
                        @if ($canRevoke)
                            <button
                                type="button"
                                onclick="document.getElementById('revoke-modal').classList.remove('hidden')"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-orange-600 text-white font-semibold transition hover:bg-orange-700"
                            >
                                Revoke Approval
                            </button>
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <div class="grid gap-8 mt-10 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-8">
                <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-2xl font-bold text-white">Request Details</h2>

                    <div class="grid gap-4 mt-6 md:grid-cols-2">
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Requested By</p>
                            <p class="text-white font-semibold mt-2">{{ $leaveRequest->user?->name }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Department</p>
                            <p class="text-white font-semibold mt-2">{{ $leaveRequest->user?->structure?->name ?? 'Not assigned' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Requested Days</p>
                            <p class="text-white font-semibold mt-2">{{ $leaveRequest->requested_days ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Submitted</p>
                            <p class="text-white font-semibold mt-2">{{ \Carbon\Carbon::parse($leaveRequest->created_at)->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg bg-gray-900/70 border border-gray-700 p-5">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Employee Reason</p>
                        <p class="text-gray-200 mt-3">{{ $leaveRequest->reason ?: 'No reason provided.' }}</p>
                    </div>

                    <div class="mt-6 rounded-lg bg-gray-900/70 border border-gray-700 p-5">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Approver Reason</p>
                        <p class="text-gray-200 mt-3">{{ $approverReason?->note ?: 'No approver reason available yet.' }}</p>
                        @if ($approverReason)
                            <p class="text-slate-400 text-sm mt-3">
                                {{ $approverReason->approver?->name ?? 'Approver' }}
                                •
                                {{ optional($approverReason->acted_at)->format('M d, Y h:i A') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-2xl font-bold text-white">Approval Flow</h2>

                    <div class="space-y-4 mt-6">
                        @foreach ($leaveRequest->processSteps as $step)
                            @php
                                $badgeClass = $statusClasses[$step['status']] ?? 'bg-slate-600 text-white';
                                $cardClass = $step['is_current'] ? 'border-blue-500/60 ring-2 ring-blue-500/20' : 'border-gray-700';
                            @endphp

                            <div class="rounded-lg border {{ $cardClass }} bg-gray-900/60 p-5">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                            {{ $step['step'] + 1 }}
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center gap-3">
                                                <h3 class="text-lg font-semibold text-white">{{ $step['title'] }}</h3>
                                                <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                                                    {{ str($step['status'])->headline() }}
                                                </span>
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

            <div class="space-y-4">
                <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Current Step</p>
                    <p class="text-white font-semibold mt-2">Step {{ $leaveRequest->current_step }}</p>
                </div>
                <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Approval Levels</p>
                    <p class="text-white font-semibold mt-2">{{ $leaveRequest->processSteps->count() - 1 }}</p>
                </div>
                <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Balance Impact</p>
                    <p class="text-white font-semibold mt-2">
                        {{ $leaveRequest->deductsFromBalance() ? 'Deducts from leave balance' : 'Does not deduct from leave balance' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @can(\App\Enums\PermissionType::LeaveRequestRevoke)
        @if ($canRevoke)
            <div id="revoke-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
                <div class="bg-gray-800 rounded-xl p-8 w-full max-w-md ring-2 ring-orange-500/40">
                    <h2 class="text-xl font-bold text-white mb-2">Revoke Approved Leave</h2>
                    <p class="text-slate-400 text-sm mb-6">
                        This will reject the approved leave, refund the balance, and notify the employee.
                        This action cannot be undone.
                    </p>

                    <form method="POST" action="{{ route('leave-requests.revoke', $leaveRequest) }}">
                        @csrf
                        @method('PATCH')
                        <div class="flex justify-end gap-3 mt-6">
                            <button
                                type="button"
                                onclick="document.getElementById('revoke-modal').classList.add('hidden')"
                                class="px-4 py-2 rounded-lg bg-gray-700 text-white font-semibold hover:bg-gray-600 transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-lg bg-orange-600 text-white font-semibold hover:bg-orange-700 transition"
                            >
                                Revoke Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endcan
</x-auth-layout>
