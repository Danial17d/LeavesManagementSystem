<x-auth-layout>
    <x-error/>
    <x-status/>

    @php
        $statusClasses = [
            'pending' => 'bg-amber-500 text-gray-950',
            'waiting' => 'bg-slate-600 text-white',
            'approved' => 'bg-emerald-600 text-white',
            'rejected' => 'bg-red-600 text-white',
        ];
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white">Manager Leave Approvals</h1>
            <p class="text-slate-400 mt-2">Review leave requests assigned to you and record an approval decision.</p>
        </div>

        @forelse ($approvalRequests as $approvalRequest)
            @php
                $approvable = $approvalRequest->approvable;
                $isStructureRequest = $approvable instanceof \App\Models\StructureRequest;
            @endphp

            <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">Employee</p>
                                <h2 class="text-2xl font-bold text-white">{{ $approvable->user?->name }}</h2>
                            </div>
                            <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold {{ $statusClasses[$approvalRequest->status] ?? 'bg-slate-600 text-white' }}">
                                {{ str($approvalRequest->status)->headline() }}
                            </span>
                            @if ($isStructureRequest)
                                <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold bg-blue-600/20 border border-blue-500/40 text-blue-300">
                                    Structure Request
                                </span>
                            @else
                                <a href="{{ route('leave-requests.show', $approvable) }}" class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold bg-gray-700 border border-gray-600 text-slate-300 hover:bg-gray-600 transition">
                                    View Request &rarr;
                                </a>
                            @endif
                        </div>

                        @if ($isStructureRequest)
                            <p class="text-slate-400 mt-2">
                                {{ str($approvable->type)->headline() }} &rarr; {{ $approvable->structure?->name }}
                            </p>
                        @else
                            <p class="text-slate-400 mt-2">
                                {{ $approvable->leaveType?->name ?? 'Leave Request' }}
                                |
                                {{ \Carbon\Carbon::parse($approvable->from)->format('M d, Y') }}
                                to
                                {{ \Carbon\Carbon::parse($approvable->to)->format('M d, Y') }}
                            </p>
                        @endif

                        @if ($approvable->reason)
                            <p class="text-gray-300 mt-4 max-w-3xl">{{ $approvable->reason }}</p>
                        @endif

                        @if (!$isStructureRequest && $approvable->attachment)
                            <div class="mt-4">
                                <p class="text-xs uppercase tracking-wide text-slate-400">Employee Attachment</p>
                                <a
                                    href="{{ route('leave-requests.attachment', $approvable) }}"
                                    class="inline-flex items-center mt-2 rounded-lg border border-blue-500/40 bg-blue-500/10 px-4 py-2 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/20"
                                >
                                    Download Attachment
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-full xl:min-w-[360px] xl:max-w-md">
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                {{ $isStructureRequest ? 'Target Structure' : 'Department' }}
                            </p>
                            <p class="text-white font-semibold mt-2">
                                @if ($isStructureRequest)
                                    {{ $approvable->structure?->name ?? 'N/A' }}
                                @else
                                    {{ $approvable->user?->structure?->name ?? 'Not assigned' }}
                                @endif
                            </p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Current Step</p>
                            <p class="text-white font-semibold mt-2">Step {{ $approvalRequest->step }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Submitted</p>
                            <p class="text-white font-semibold mt-2">{{ \Carbon\Carbon::parse($approvable->created_at)->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <x-divider/>

                <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_360px]">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-4">Approval Flow</h3>

                        @if ($isStructureRequest)
                            <div class="rounded-lg border border-blue-500/60 ring-2 ring-blue-500/20 bg-gray-900/60 p-5">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h4 class="text-lg font-semibold text-white">Step 1</h4>
                                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-semibold {{ $statusClasses[$approvalRequest->status] ?? 'bg-slate-600 text-white' }}">
                                        {{ str($approvalRequest->status)->headline() }}
                                    </span>
                                    <span class="inline-flex items-center rounded-lg bg-blue-600/20 px-3 py-1 text-xs font-semibold text-blue-300 border border-blue-500/40">
                                        Current Step
                                    </span>
                                </div>
                                <p class="text-gray-200 mt-2">{{ $approvalRequest->approver?->name }}</p>
                                <p class="text-slate-400 text-sm mt-1">Structure Manager</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach (($approvable->processSteps ?? collect()) as $step)
                                    @php
                                        $badgeClass = $statusClasses[$step['status']] ?? 'bg-slate-600 text-white';
                                        $cardClass = $step['is_current']
                                            ? 'border-blue-500/60 ring-2 ring-blue-500/20'
                                            : 'border-gray-700';
                                    @endphp

                                    <div class="rounded-lg border {{ $cardClass }} bg-gray-900/60 p-5">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <h4 class="text-lg font-semibold text-white">{{ $step['title'] }}</h4>
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

                                            <div class="text-sm text-slate-400 lg:text-right">
                                                @if ($step['acted_at'])
                                                    <p>{{ \Carbon\Carbon::parse($step['acted_at'])->format('M d, Y h:i A') }}</p>
                                                @elseif ($step['status'] === 'waiting')
                                                    <p>Waiting for previous approvals</p>
                                                @else
                                                    <p>Awaiting action</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="rounded-lg border border-gray-700 bg-gray-900/60 p-6">
                            <h3 class="text-xl font-bold text-white">Decision</h3>
                            <p class="text-slate-400 mt-2">Add an optional note, then approve or reject this request.</p>

                            <form method="POST" action="{{ route('leave-approvals.update', $approvalRequest) }}" class="mt-6 space-y-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="note-{{ $approvalRequest->id }}" class="block text-sm font-medium text-slate-300 mb-2">Manager note</label>
                                    <textarea
                                        id="note-{{ $approvalRequest->id }}"
                                        name="note"
                                        rows="5"
                                        class="w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white focus:border-blue-500 focus:outline-none"
                                        placeholder="Add context for the employee or the next approver"
                                    >{{ old('note') }}</textarea>
                                </div>
                                @if($approvalRequest->approvable instanceof \App\Models\StructureRequest)
                                    <div>
                                        <x-input label="Salary" name="salary" type="number" value="{{old('salary')}}" />
                                    </div>
                                @endif

                                @php
                                    $isDecided = in_array($approvalRequest->status, ['approved', 'rejected']);
                                @endphp

                                <div class="flex flex-col gap-3">
                                    <button
                                        type="submit"
                                        name="decision"
                                        value="approved"
                                        {{ $isDecided ? 'disabled' : '' }}
                                        class="inline-flex items-center justify-center px-5 py-3 bg-emerald-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-emerald-600"
                                    >
                                        Approve Request
                                    </button>

                                    <button
                                        type="submit"
                                        name="decision"
                                        value="rejected"
                                        {{ $isDecided ? 'disabled' : '' }}
                                        class="inline-flex items-center justify-center px-5 py-3 bg-red-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-600"
                                    >
                                        Reject Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                <h2 class="text-2xl font-bold text-white">No Pending Approvals</h2>
                <p class="text-slate-400 mt-2">New leave requests assigned to you will appear here.</p>
            </div>
        @endforelse
    </div>
</x-auth-layout>
