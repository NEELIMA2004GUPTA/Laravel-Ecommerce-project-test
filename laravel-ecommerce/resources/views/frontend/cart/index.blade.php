<x-app-layout>
<div class="max-w-5xl mx-auto py-8">

<h2 class="text-2xl font-bold mb-6">🛒 Your Shopping Cart</h2>

@if(count($cart) > 0)
<table class="w-full border text-left rounded-lg overflow-hidden">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-3">Product</th>
            <th class="p-3">Qty</th>
            <th class="p-3">Price</th>
            <th class="p-3">Remove</th>
        </tr>
    </thead>

    <tbody>

        @php $total = 0; $items = 0; @endphp

        @foreach($cart as $id => $item)
            @php 
                $originalPrice = $item['original_price'] ?? $item['price']; 
                $discount = $item['discount'] ?? 0;
                $lineTotal = $item['price'] * $item['qty']; 
                $total += $lineTotal;
                $items += $item['qty'];
            @endphp

            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-medium">{{ $item['title'] }}</td>

                <td class="p-3">
                    <form method="POST" action="{{ route('cart.update', $id) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="number" name="qty" value="{{ $item['qty'] }}" min="1"
                               class="w-16 border rounded p-1">
                        <button class="text-blue-600 hover:underline">Update</button>
                    </form>
                </td>

                <td class="p-3 font-semibold">
                    <div>
                        <span class="text-lg text-blue-600">₹{{ number_format($item['price'], 2) }}</span>

                        @if($discount > 0)
                            <span class="text-sm text-gray-400 line-through block">₹{{ number_format($originalPrice, 2) }}</span>
                            <span class="text-xs text-red-500 font-bold">{{ $discount }}% OFF</span>
                        @endif
                    </div>
                </td>

                <td class="p-3">
                    <a href="{{ route('cart.remove', $id) }}" class="text-red-600 font-bold hover:underline">×</a>
                </td>
            </tr>

        @endforeach

    </tbody>
</table>

<!-- Order Summary -->
<div class="mt-6 border-t pt-4 text-right">
    <p class="text-lg font-medium">Total Items: <span class="font-bold">{{ $items }}</span></p>
    <p class="text-2xl font-bold text-green-600 mt-1">Amount Payable: ₹{{ number_format($total, 2) }}</p>

    <a href="#" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700 transition">
        Proceed to Checkout →
    </a>
</div>

@else
    <p class="text-gray-500 text-lg">Your cart is currently empty.</p>

    <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">
        ← Continue Shopping
    </a>
@endif

</div>
</x-app-layout>
