<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-6">
        <h1 class="text-2xl font-bold mb-6">Manage Orders</h1>
        <form method="GET" action="{{ route('admin.orders') }}" class="mb-4 flex gap-3">

            <input type="text" name="search" value="{{ $search }}" placeholder="Search by Customer Name"
            class="border p-2 rounded w-60">

            <select name="status" class="border p-2 rounded w-52">
                <option value="">All Status</option>
                <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Confirmed" {{ $status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Shipped" {{ $status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="Delivered" {{ $status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="Cancelled" {{ $status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Filter
            </button>

        </form>
        <br>

        <table class="w-full bg-white shadow rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Order ID</th>
                    <th class="p-3 text-left">Customer</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Placed On</th>
                    <th class="p-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">#{{ $order->id }}</td>
                    <td class="p-3">{{ $order->user->name }}</td>
                    @php
                        $subTotal = $order->items->sum(function($item){
                            return $item->quantity * $item->price;
                        });

                        $tax = ($subTotal * 5) / 100; 

                        $grandTotal = $subTotal + $tax;
                    @endphp
                    <td class="p-3">₹{{ number_format($grandTotal, 2) }}</td>
                    <td class="p-3">{{ $order->status }}</td>
                    <td class="p-3">{{ $order->created_at->format('d M, Y') }}</td>
                    <td class="p-3 text-center">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:underline">
                            View Details
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-5">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
