<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-10">

        <h1 class="text-3xl font-bold mb-8">My Orders</h1>
        <!-- Back to Products -->
        <a href="{{ route('products.index') }}" 
           class="inline-block mb-6 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
           ← Back to Products
        </a>

        <!-- Flash Messages -->
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

        <!-- No Orders -->
        @if($orders->count() == 0)
            <div class="text-center py-20">
                <p class="text-gray-600 text-xl">You haven't placed any orders yet.</p>
            </div>
        @else
            <div class="space-y-8">
                @foreach($orders as $order)
                <div class="bg-white border rounded-lg shadow hover:shadow-lg transition p-6">

                    <!-- Order Header -->
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold">Order #{{ $order->id }}</h2>
                        <p class="text-sm text-gray-500">
                            {{ $order->created_at->format('d M Y') }} • {{ $order->created_at->format('l') }}
                        </p>

                        <div class="flex items-center gap-3">
                            <!-- Status Badge -->
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                @if($order->status == 'Pending') bg-yellow-100 text-yellow-700
                                @elseif($order->status == 'Confirmed') bg-yellow-100 text-yellow-700
                                @elseif($order->status == 'Shipped') bg-blue-100 text-blue-700
                                @elseif($order->status == 'Delivered') bg-green-100 text-green-700
                                @elseif($order->status == 'Cancelled') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                @if($order->status == 'Confirmed')
                                    Approved
                                @else
                                    {{ ucfirst($order->status) }}
                                @endif
                            </span>

                            @if(in_array($order->status, ['Pending','Confirmed']))
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded">
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="text-sm text-gray-600 mt-3">
                        <p><strong>Name:</strong> {{ $order->name }}</p>
                        <p><strong>Phone:</strong> {{ $order->country_code ?? '' }} {{ $order->phone }}</p>
                        <p><strong>Address:</strong> {{ $order->address }}, {{ $order->pincode }}</p>
                        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                    </div>

                    <!-- Items List -->
                    <div class="mt-5 border-t pt-4 space-y-4">
                        <h3 class="font-semibold mb-3 text-gray-700">Order Items</h3>

                        @foreach($order->items as $item)
                        <div class="flex items-center gap-4 bg-gray-50 rounded-lg p-3 border">

                            @php
                                $product = $item->product;
                                $imgs = is_array($item->product->images)
                                    ? $item->product->images
                                    : json_decode($item->product->images, true);
                                $img = $imgs[0] ?? null;
                            @endphp

                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $img ? asset('storage/' . $img) : asset('/no-image.png') }}"
                                        class="w-16 h-16 object-cover rounded cursor-pointer hover:scale-105 transition" />
                            </a>

                            <div class="flex-1">
                                <p class="font-medium">{{ $item->product->title }}</p>
                                <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                <p class="font-semibold text-gray-800">₹{{ $item->price }}</p>
                            </div>

                            @if($order->status == 'Delivered')
                            <div class="flex-shrink-0">
                                <button data-toggle="collapse" data-target="#reviewBox-{{ $item->id }}"
                                    class="px-3 py-1 bg-indigo-600 text-white rounded">
                                    Add Review
                                </button>
                            </div>
                            @endif
                        </div>

                        @if($order->status == 'Delivered')
                        <div id="reviewBox-{{ $item->id }}" class="mt-3 border p-4 rounded bg-white hidden">
                            @if($item->product)
                                @include('reviews._form', ['product' => $item->product])
                            @endif
                        </div>
                        @endif

                        @endforeach
                    </div>

                    <!-- Totals -->
                    @php
                        $subTotal = $order->items->sum(fn($item) => $item->quantity * $item->price);
                        $tax = ($subTotal * 5) / 100;
                        $grandTotal = $subTotal + $tax;
                    @endphp
                    <div class="mt-4 text-right text-xl font-bold text-gray-800">
                        Subtotal: ₹{{ number_format($subTotal, 2) }} <br>
                        Tax (5%): ₹{{ number_format($tax, 2) }} <br>
                        Grand Total: ₹{{ number_format($grandTotal, 2) }}
                    </div>

                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Collapse Script -->
    <script>
    document.querySelectorAll('[data-toggle="collapse"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.querySelector(btn.dataset.target);
            target.classList.toggle('hidden');
            if (!target.classList.contains('hidden')) {
                const firstInput = target.querySelector('input, textarea, select');
                if (firstInput) firstInput.focus();
            }
        });
    });
    </script>
</x-app-layout>
