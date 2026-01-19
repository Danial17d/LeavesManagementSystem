<x-layout1>
    <div class="max-w-4xl mx-auto mt-10 mb-10 px-4">

        <!-- Hero -->
        <div
            class="bg-gray-800 text-white p-6 rounded-lg text-center
                   transition duration-300 ease-in-out
                   hover:-translate-y-1 hover:shadow-lg
                   hover:ring-2 hover:ring-blue-500/40">
            <h1 class="text-2xl font-bold">
                Welcome to the Leave Management System
            </h1>
            <p class="mt-2 text-gray-300">
                The best place to track your request approvals
            </p>
        </div>

        <!-- Features -->
        <div class="bg-gray-800 mt-4 rounded-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">

                <div
                    class="bg-gray-700 rounded-lg p-6 text-center
                           transition duration-300 ease-in-out
                           hover:-translate-y-1 hover:shadow-lg
                           hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-xl font-semibold text-white">
                        Easy Leave Requests
                    </h2>
                    <p class="text-gray-300 mt-2">
                        Submit leave requests quickly with a simple and clear process.
                    </p>
                </div>

                <div
                    class="bg-gray-700 rounded-lg p-6 text-center
                           transition duration-300 ease-in-out
                           hover:-translate-y-1 hover:shadow-lg
                           hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-xl font-semibold text-white">
                        Transparent Approvals
                    </h2>
                    <p class="text-gray-300 mt-2">
                        Know exactly where your request stands at every step.
                    </p>
                </div>

                <div
                    class="bg-gray-700 rounded-lg p-6 text-center
                           transition duration-300 ease-in-out
                           hover:-translate-y-1 hover:shadow-lg
                           hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-xl font-semibold text-white">
                        Real-Time Tracking
                    </h2>
                    <p class="text-gray-300 mt-2">
                        Track approval status without emails or paperwork.
                    </p>
                </div>

                <div
                    class="bg-gray-700 rounded-lg p-6 text-center
                           transition duration-300 ease-in-out
                           hover:-translate-y-1 hover:shadow-lg
                           hover:ring-2 hover:ring-blue-500/40">
                    <h2 class="text-xl font-semibold text-white">
                        Secure & Reliable
                    </h2>
                    <p class="text-gray-300 mt-2">
                        Your data is protected with modern security standards.
                    </p>
                </div>

                <!-- CTA -->
                <div class="col-span-full p-5 flex justify-center">
                    <x-button href="/register">
                        Get Started
                    </x-button>
                </div>

            </div>
        </div>

    </div>
</x-layout1>
