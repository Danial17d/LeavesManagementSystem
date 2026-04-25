<x-auth-layout>
    <x-error/>
    <x-status/>

    @php
        $statusClasses = [
            'submitted' => 'bg-sky-600 text-white',
            'pending'   => 'bg-amber-500 text-gray-950',
            'waiting'   => 'bg-slate-600 text-white',
            'approved'  => 'bg-emerald-600 text-white',
            'rejected'  => 'bg-red-600 text-white',
            'cancelled' => 'bg-gray-500 text-white',
        ];
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Structure Transfer Requests</h1>
                    <p class="text-slate-400 mt-2">Track every structure transfer request from submission to final approval.</p>
                </div>
                <a href="{{ route('structure-requests.create') }}"
                   class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                    New Request
                </a>
            </div>
        </div>

        @forelse ($structureRequests as $structureRequest)
            <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-2xl font-bold text-white">
                            {{ $structureRequest->structure?->name ?? 'Structure Request' }}
                        </h2>
                        <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold {{ $statusClasses[$structureRequest->status] ?? 'bg-slate-600 text-white' }}">
                            {{ str($structureRequest->status)->headline() }}
                        </span>
                    </div>

                    <p class="text-slate-400 mt-2">
                        Submitted {{ \Carbon\Carbon::parse($structureRequest->created_at)->format('M d, Y') }}
                    </p>
                    <div class="mt-4">
                        <a
                            href="{{ route('structure-requests.show', $structureRequest) }}"
                            class="inline-flex items-center rounded-lg border border-blue-500/40 bg-blue-500/10 px-4 py-2 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/20"
                        >
                            View Details
                        </a>
                    </div>
                    @if ($structureRequest->reason)
                        <p class="text-gray-300 mt-4 max-w-3xl">{{ $structureRequest->reason }}</p>
                    @endif
                </div>

                <x-divider/>

                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Approval Flow</h3>

                    <div class="space-y-4">
                        @forelse ($structureRequest->approval as $approval)
                            @php
                                $badgeClass = $statusClasses[$approval->status] ?? 'bg-slate-600 text-white';
                                $isCurrent  = $approval->step == $structureRequest->current_step;
                                $cardClass  = $isCurrent
                                    ? 'border-blue-500/60 ring-2 ring-blue-500/20'
                                    : 'border-gray-700';
                            @endphp

                            <div class="rounded-lg border {{ $cardClass }} bg-gray-900/60 p-5">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                            {{ $approval->step + 1 }}
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center gap-3">
                                                <h4 class="text-lg font-semibold text-white">
                                                    Step {{ $approval->step + 1 }}
                                                </h4>
                                                <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                                                    {{ str($approval->status)->headline() }}
                                                </span>
                                                @if ($isCurrent)
                                                    <span class="inline-flex items-center rounded-lg bg-blue-600/20 px-3 py-1 text-xs font-semibold text-blue-300 border border-blue-500/40">
                                                        Current Step
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($approval->approver)
                                                <p class="text-gray-200 mt-2">{{ $approval->approver->name }}</p>
                                            @else
                                                <p class="text-slate-500 mt-2 italic">Approver not yet assigned</p>
                                            @endif

                                            @if ($approval->note)
                                                <p class="text-slate-300 text-sm mt-3">{{ $approval->note }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-sm text-slate-400 lg:text-right">
                                        @if ($approval->updated_at && in_array($approval->status, ['approved', 'rejected']))
                                            <p>{{ \Carbon\Carbon::parse($approval->updated_at)->format('M d, Y h:i A') }}</p>
                                        @elseif ($approval->status === 'waiting')
                                            <p>Waiting for previous approvals</p>
                                        @elseif ($approval->status === 'pending')
                                            <p>Waiting for action</p>
                                        @else
                                            <p>Submitted {{ \Carbon\Carbon::parse($structureRequest->created_at)->format('M d, Y h:i A') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400">No approval steps recorded yet.</p>
                        @endforelse
                    </div>
                </div>

                <x-divider/>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Requested By</p>
                        <p class="text-white font-semibold mt-2">{{ $structureRequest->user?->name }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Approvals</p>
                        <p class="text-white font-semibold mt-2">{{ $structureRequest->approval->count() }} Levels</p>
                    </div>
                </div>

                @if (in_array($structureRequest->status, ['submitted', 'pending', 'waiting']))
                    <x-divider/>

                    <form method="POST" action="{{ route('structure-requests.destroy', $structureRequest) }}"
                          onsubmit="return confirm('Cancel this request?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center px-5 py-2 bg-red-600/20 border border-red-500/40 text-red-300 font-semibold rounded-lg transition hover:bg-red-600/30">
                            Cancel Request
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="bg-gray-800 rounded-lg p-8 mt-10 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                <h2 class="text-2xl font-bold text-white">No Structure Requests Yet</h2>
                <p class="text-slate-400 mt-2">Submit a request to be transferred to a different department or structure.</p>
                <div class="pt-6">
                    <a href="{{ route('structure-requests.create') }}"
                       class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                        New Request
                    </a>
                </div>
            </div>
        @endforelse

        @if ($structureRequests->hasPages())
            <div class="mt-10">
                {{ $structureRequests->links() }}
            </div>
        @endif
    </div>
</x-auth-layout>
