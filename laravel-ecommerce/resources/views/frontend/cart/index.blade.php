<x-app-layout>
<div class="max-w-5xl mx-auto py-8">

<h2 class="text-2xl font-bold mb-6">🛒 Your Shopping Cart</h2>

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

@if(count($cart) > 0)

<table class="w-full border text-left rounded-lg overflow-hidden">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-3">Product</th>
            <th class="p-3">Qty</th>
            <th class="p-3">Price</th>
            <th class="p-3">Total Price</th>
        </tr>
    </thead>

    <tbody>

        @php $total = 0; $items = 0; @endphp

        @foreach($cart as $id => $item)
            @php 
                $originalPrice = $item['original_price'] ?? $item['price']; 
                $discount = $item['discount'] ?? 0;
                $lineTotal = $item['price'] * $item['quantity']; 
                $total += $lineTotal;
                $items += $item['quantity'];
            @endphp

            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-medium">{{ $item['title'] }}</td>

                <td class="p-3">
                    <form method="POST" action="{{ route('cart.update', $id) }}" class="flex items-center gap-2">
                        @csrf
                       <input type="number" class="qty-input border p-1 w-16"
                            data-id="{{ $id }}"
                            value="{{ $item['quantity'] }}"
                            min="1"
                            max="{{ $item['stock'] }}">

                        <a href="{{ route('cart.remove', $id) }}" class="text-red-600 font-bold hover:underline">x</a>
                    </form>
                </td>

                <td class="p-3 font-semibold">
                    <span class="text-lg text-blue-600">₹{{ number_format($item['price'], 2) }}</span>

                    @if($discount > 0)
                        <span class="text-sm text-gray-400 line-through block">₹{{ number_format($originalPrice, 2) }}</span>
                        <span class="text-xs text-red-500 font-bold">{{ $discount }}% OFF</span>
                    @endif
                </td>

                <td class="item-subtotal" data-id="{{ $id }}">
                    ₹{{ number_format($lineTotal, 2) }}
                </td>
            </tr>
        @endforeach

    </tbody>
</table>

{{-- CALCULATION --}}
@php
    $discountAmount = session('coupon.discount') ?? 0;
    $subTotal = $total - $discountAmount;
    $tax = $subTotal * 0.05; // 5% GST
    $grandTotal = $subTotal + $tax;
@endphp

{{-- COUPON FORM --}}

@if(!session()->has('coupon'))
<form action="{{ route('apply.coupon') }}" method="POST" class="my-6 flex items-center gap-3">
    @csrf

    <div class="relative flex-1">
        <input type="text" name="coupon_code"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition placeholder-gray-400"
            placeholder="Enter coupon code"
            required>
    </div>

    <button type="submit"
        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-transform transform hover:scale-105">
        Apply
    </button>
</form>
@endif

@if(session()->has('coupon'))
<a href="{{ route('remove.coupon') }}" class="text-red-600 text-sm hover:underline">
   Remove Coupon
</a>
@endif

{{-- ORDER SUMMARY --}}
<div class="mt-6 border-t pt-4 text-right">
    <p class="text-lg font-medium">
        Total Items: <span id="cart-items" class="font-bold">{{ $items }}</span>
    </p>

    <div class="flex justify-between text-gray-700 my-2">
        <span>Subtotal:</span>
        <span id="cart-total">
            ₹{{ number_format($subTotal, 2) }}
        </span>
    </div>

    <div class="flex justify-between text-gray-700 my-2">
        <span>Tax (5% GST):</span>
        <span id="cart-tax">₹{{ number_format($tax, 2) }}</span>
    </div>
    
    @if(session()->has('coupon'))
        <div class="flex justify-between text-green-600 font-semibold my-2">
            <span>Coupon ({{ session('coupon.code') }})</span>
            <span>- ₹{{ number_format($discountAmount, 2) }}</span>
        </div>
    @endif

    <p class="text-2xl font-bold text-green-600 mt-1">
        Amount Payable: <span id="cart-grand-total">₹{{ number_format($grandTotal, 2) }}</span>
    </p>

    <a href="{{ auth()->check() ? route('checkout') : route('login') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium inline-block transition mt-3">
        Proceed to Checkout
    </a>
</div>

@else
<p class="text-gray-500 text-lg">Your cart is currently empty.</p>
<a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">← Continue Shopping</a>
@endif

</div>
<script>
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function() {
        let id = this.dataset.id;
        let qty = this.value;

        fetch(`/cart/update/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ qty: qty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {

                // Update line subtotal
                document.querySelector(`.item-subtotal[data-id="${id}"]`).innerText = `₹${data.subtotal}`;

                // Update cart totals
                document.getElementById('cart-total').innerText = `₹${data.total}`;
                document.getElementById('cart-tax').innerText = `₹${data.tax}`;
                document.getElementById('cart-grand-total').innerText = `₹${data.grandTotal}`;
                document.getElementById('cart-items').innerText = data.totalItems;
            }
        });
    });
});
</script>

</x-app-layout>



