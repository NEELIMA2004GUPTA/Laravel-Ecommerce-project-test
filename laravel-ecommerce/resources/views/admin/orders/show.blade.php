<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-6">
        <h1 class="text-2xl font-bold mb-6">Order #{{ $order->id }}</h1>
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">
            {{ session('error') }}
        </div>
    @endif

        <!-- Order Summary -->
        <div class="bg-white shadow rounded p-6 mb-6">
            <p><strong>Customer:</strong> {{ $order->user->name }}</p>
            <p><strong>Phone:</strong> {{ $order->phone }}</p>
            <p><strong>Address:</strong> {{ $order->address }}</p>
            <p><strong>Payment:</strong> {{ $order->payment_method }}</p>
            <p><strong>Status:</strong> {{ $order->status }}</p>
            <p><strong>Placed on:</strong> {{ $order->created_at->format('d M, Y h:i A') }}</p>
        </div>

        <!-- Update Status -->
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mb-6">
            @csrf
            <label class="font-semibold">Update Status:</label>
            <select name="status" class="border rounded p-2">
                <option {{ $order->status == 'Pending' ? 'selected':'' }}>Pending</option>
                <option {{ $order->status == 'Confirmed' ? 'selected':'' }}>Confirmed</option>
                <option {{ $order->status == 'Shipped' ? 'selected':'' }}>Shipped</option>
                <option {{ $order->status == 'Delivered' ? 'selected':'' }}>Delivered</option>
                <option {{ $order->status == 'Cancelled' ? 'selected':'' }}>Cancelled</option>
            </select>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                Update
            </button>
        </form>

        <!-- Order Items -->
        <h2 class="text-xl font-semibold mb-3">Order Items</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        @foreach($order->items as $item)
            <div class="flex gap-4 p-3 border rounded-lg hover:shadow">
                @php
                    $imgs = is_array($item->product->images) ? $item->product->images : json_decode($item->product->images, true);
                    $img = $imgs[0] ?? null;
                @endphp

                <img src="{{ $img ? asset('storage/' . $img) : asset('/no-image.png') }}"
                     class="w-20 h-20 rounded object-cover">

                <div>
                    <p class="font-semibold text-gray-800">{{ $item->product->title }}</p>
                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                    <p class="text-blue-600 font-bold">₹{{ $item->price }}</p>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-right text-2xl font-bold mt-6 border-t pt-4">
    @php
        $subTotal = $order->items->sum(function($item){
            return $item->quantity * $item->price;
        });

        $tax = ($subTotal * 5) / 100; // 18% GST

        $grandTotal = $subTotal + $tax;
    @endphp
        <p>Subtotal: ₹{{ number_format($subTotal, 2) }}</p>
        <p>Tax (5%): ₹{{ number_format($tax, 2) }}</p>
        <p class="text-green-600">Grand Total: ₹{{ number_format($grandTotal, 2) }}</p>
    </div>
</div>
</x-app-layout>
