<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-semibold mb-4">Hello, {{ auth()->user()->name }} 👋</h2>

                <div class="grid grid-cols-5 gap-4 text-center">

                    <div class="p-4 bg-blue-100 rounded">
                        <h3 class="text-lg font-medium">Total Orders</h3>
                        <p class="text-2xl font-bold">{{ $totalOrders }}</p>
                    </div>

                    <div class="p-4 bg-yellow-100 rounded">
                        <h3 class="text-lg font-medium">Pending</h3>
                        <p class="text-2xl font-bold">{{ $pending }}</p>
                    </div>

                    <div class="p-4 bg-green-100 rounded">
                        <h3 class="text-lg font-medium">Delivered</h3>
                        <p class="text-2xl font-bold">{{ $delivered }}</p>
                    </div>

                    <div class="p-4 bg-pink-100 rounded">
                        <h3 class="text-lg font-medium">Shipped</h3>
                        <p class="text-2xl font-bold">{{ $shipped }}</p>
                    </div>

                    <div class="p-4 bg-red-100 rounded">
                        <h3 class="text-lg font-medium">Cancelled</h3>
                        <p class="text-2xl font-bold">{{ $cancelled }}</p>
                    </div>

                </div>

                <!-- Support Section (Text Only) -->
                <div class="mt-10 p-4 bg-gray-100 border rounded">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Support / Help Center</h3>
                    <p class="text-gray-700">For support or help, contact us:</p>
                    <p class="mt-2 text-gray-900 font-medium">📞 Phone: <span class="font-bold">+91 9876543210</span></p>
                    <p class="text-gray-900 font-medium">📧 Email: <span class="font-bold">support@example.com</span></p>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
