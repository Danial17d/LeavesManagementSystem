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
        $typeLabel = $structureRequest->type === 'move' ? 'Transfer Request' : 'Assignment Request';
        $canCancel = in_array($structureRequest->status, ['submitted', 'pending', 'waiting'], true);
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-16">


        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <a href="{{ route('structure-requests.index') }}"
                       class="inline-flex items-center text-sm font-semibold text-blue-300 transition hover:text-blue-200">
                        &larr; Back to requests
                    </a>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-bold text-white">
                            {{ $structureRequest->structure?->name ?? 'Structure Request' }}
                        </h1>
                        <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold {{ $statusClasses[$structureRequest->status] ?? 'bg-slate-600 text-white' }}">
                            {{ str($structureRequest->status)->headline() }}
                        </span>
                        <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold bg-gray-700 text-gray-200">
                            {{ $typeLabel }}
                        </span>
                    </div>
                    <p class="text-slate-400 mt-2">
                        Submitted {{ \Carbon\Carbon::parse($structureRequest->created_at)->format('M d, Y') }}
                    </p>
                </div>

                @if ($canCancel)
                    <form method="POST" action="{{ route('structure-requests.destroy', $structureRequest) }}"
                          onsubmit="return confirm('Cancel this request?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-red-600 text-white font-semibold transition hover:bg-red-700">
                            Cancel Request
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid gap-8 mt-10 xl:grid-cols-[minmax(0,1fr)_300px]">


            <div class="space-y-8">


                <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-2xl font-bold text-white">Request Details</h2>

                    <div class="grid gap-4 mt-6 md:grid-cols-2">
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Requested By</p>
                            <p class="text-white font-semibold mt-2">{{ $structureRequest->user?->name }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Target Structure</p>
                            <p class="text-white font-semibold mt-2">{{ $structureRequest->structure?->name ?? '—' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Request Type</p>
                            <p class="text-white font-semibold mt-2">{{ $typeLabel }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-900/70 border border-gray-700 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Submitted</p>
                            <p class="text-white font-semibold mt-2">{{ \Carbon\Carbon::parse($structureRequest->created_at)->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg bg-gray-900/70 border border-gray-700 p-5">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Reason</p>
                        <p class="text-gray-200 mt-3">{{ $structureRequest->reason ?: 'No reason provided.' }}</p>
                    </div>
                </div>


                <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-2xl font-bold text-white">Approval Flow</h2>

                    <div class="space-y-4 mt-6">
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
                                                <h3 class="text-lg font-semibold text-white">Step {{ $approval->step + 1 }}</h3>
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
                                        @if ($approval->acted_at)
                                            <p>{{ \Carbon\Carbon::parse($approval->acted_at)->format('M d, Y h:i A') }}</p>
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
            </div>

            <div class="space-y-4">
                <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Status</p>
                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold mt-2 {{ $statusClasses[$structureRequest->status] ?? 'bg-slate-600 text-white' }}">
                        {{ str($structureRequest->status)->headline() }}
                    </span>
                </div>
                <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Current Step</p>
                    <p class="text-white font-semibold mt-2">Step {{ $structureRequest->current_step + 1 }}</p>
                </div>
                <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Approval Levels</p>
                    <p class="text-white font-semibold mt-2">{{ $structureRequest->approval->count() }} Levels</p>
                </div>
                <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Request Type</p>
                    <p class="text-white font-semibold mt-2">{{ $typeLabel }}</p>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
