<x-app-layout>
<div class="max-w-4xl mx-auto p-6 bg-white shadow rounded">

    <h1 class="text-2xl font-bold mb-4">Notifications</h1>

    @foreach($notifications as $notification)
        <div class="p-4 border-b flex justify-between items-center">
            <div>
                <p>New Order from <strong>{{ $notification->data['user_name'] }}</strong></p>
                <p class="text-gray-500 text-sm">Order ID: #{{ $notification->data['order_id'] }} | ₹{{ $notification->data['total'] }}</p>
            </div>

            <a href="{{ route('admin.orders') }}" class="text-blue-600 hover:underline">View Order</a>
        </div>
    @endforeach

</div>
</x-app-layout>
