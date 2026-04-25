<x-auth-layout>
    <x-error/>
    <x-status/>

    <div class="max-w-3xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-lg p-8 transition duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-3xl font-bold text-white mb-2">Leave Type Details</h1>
            <p class="text-slate-400 mb-8">Inspect leave type configuration.</p>

            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-400">ID</p>
                    <p class="text-white font-semibold">{{ $leaveType->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Type</p>
                    <p class="text-white font-semibold">{{ $leaveType->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Days</p>
                    <p class="text-white font-semibold">{{ $leaveType->days }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Level</p>
                    <p class="text-white font-semibold">{{ $leaveType->approvalRule?->level ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <a href="{{ route('leave-types.edit', $leaveType) }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-blue-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg">
                    Edit
                </a>
                <a href="{{ route('leave-types.index') }}" class="inline-flex items-center justify-center px-5 py-2 h-12 bg-gray-600 text-white font-semibold rounded-lg transition duration-300 ease-in-out hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-lg">
                    Back
                </a>
            </div>
        </div>
    </div>
</x-auth-layout>
