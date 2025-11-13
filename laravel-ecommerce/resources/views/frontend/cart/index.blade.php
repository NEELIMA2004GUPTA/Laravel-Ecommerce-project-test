<x-app-layout>
<div class="max-w-5xl mx-auto py-8">

<h2 class="text-2xl font-bold mb-6">🛒 Your Shopping Cart</h2>

<div id="cart-messages"></div>
@if (session('success'))
    <div class="bg-green-100 text-green-800 p-2 rounded mb-3">
        {{ session('success') }}
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
                <td class="p-3 font-medium">
                    <a href="{{ route('product.show', $item['slug']) }}" class="text-blue-600 hover:underline">
                        {{ $item['title'] }}
                    </a>
                </td>
                <td class="p-3">
                    <form method="POST" action="{{ route('cart.update', $id) }}" class="flex items-center gap-2">
                        @csrf
                        <input type="number" class="qty-input border p-1 w-16" data-id="{{ $id }}" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}">
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
                <td class="item-subtotal" data-id="{{ $id }}">₹{{ number_format($lineTotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- COUPON FORM --}}
<div class="my-6 flex items-center gap-3">
    @csrf
    <input type="text" id="coupon_code" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter coupon code">
    <button id="apply-coupon-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg">Apply</button>
</div>

<div id="remove-coupon-div" class="hidden my-2">
    <button id="remove-coupon-btn" class="text-red-600 hover:underline">Remove Coupon</button>
</div>

{{-- ORDER SUMMARY --}}
<div class="mt-6 border-t pt-4 text-right">
    <p>Total Items: <span id="cart-items" class="font-bold">{{ $items }}</span></p>
    <div class="flex justify-between text-gray-700 my-2">
        <span>Subtotal:</span>
        <span id="cart-total">₹{{ number_format($total - (session('coupon.discount') ?? 0), 2) }}</span>
    </div>
    <div class="flex justify-between text-gray-700 my-2">
        <span>Tax (5% GST):</span>
        <span id="cart-tax">₹{{ number_format(($total - (session('coupon.discount') ?? 0)) * 0.05, 2) }}</span>
    </div>
    <div id="coupon-summary" class="flex justify-between text-green-600 font-semibold my-2 {{ session('coupon') ? '' : 'hidden' }}">
        <span>Coupon (<span id="coupon-code-text">{{ session('coupon.code') ?? '' }}</span>)</span>
        <span>- ₹<span id="coupon-discount">{{ session('coupon.discount') ?? 0 }}</span></span>
    </div>
    <p class="text-2xl font-bold text-green-600 mt-1">
        Amount Payable: <span id="cart-grand-total">₹{{ number_format(($total - (session('coupon.discount') ?? 0)) * 1.05, 2) }}</span>
    </p>
    <a href="{{ auth()->check() ? route('checkout') : route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium mt-3 inline-block">Proceed to Checkout</a>
</div>

@else
<p class="text-gray-500 text-lg">Your cart is currently empty.</p>
<a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">← Continue Shopping</a>
@endif

</div>

<script>
// Update quantity
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function() {
        let id = this.dataset.id;
        let qty = this.value;
        fetch(`/cart/update/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ qty: qty })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                document.querySelector(`.item-subtotal[data-id="${id}"]`).innerText = `₹${data.subtotal}`;
                document.getElementById('cart-total').innerText = `₹${data.total}`;
                document.getElementById('cart-tax').innerText = `₹${data.tax}`;
                document.getElementById('cart-grand-total').innerText = `₹${data.grandTotal}`;
                document.getElementById('cart-items').innerText = data.totalItems;
            }
        });
    });
});

// Apply coupon
document.getElementById('apply-coupon-btn').addEventListener('click', function(){
    let code = document.getElementById('coupon_code').value;
    fetch("{{ route('apply.coupon') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        body: JSON.stringify({ coupon_code: code })
    })
    .then(res => res.json())
    .then(data => {
        let msgDiv = document.getElementById('cart-messages');
        msgDiv.innerHTML = '';
        if(data.success){
            msgDiv.innerHTML = `<div class="bg-green-100 text-green-800 p-2 rounded mb-3">${data.message}</div>`;
            document.getElementById('cart-total').innerText = `₹${data.subTotal}`;
            document.getElementById('cart-tax').innerText = `₹${data.tax}`;
            document.getElementById('cart-grand-total').innerText = `₹${data.grandTotal}`;
            document.getElementById('coupon-code-text').innerText = data.code;
            document.getElementById('coupon-discount').innerText = data.discount;
            document.getElementById('coupon-summary').classList.remove('hidden');
            document.getElementById('remove-coupon-div').classList.remove('hidden');
        } else {
            msgDiv.innerHTML = `<div class="bg-red-100 text-red-800 p-2 rounded mb-3">${data.error}</div>`;
        }
    });
});

// Remove coupon
document.getElementById('remove-coupon-btn').addEventListener('click', function(){
    fetch("{{ route('remove.coupon') }}")
    .then(res => res.json())
    .then(data => {
        let msgDiv = document.getElementById('cart-messages');
        msgDiv.innerHTML = `<div class="bg-green-100 text-green-800 p-2 rounded mb-3">${data.message}</div>`;
        document.getElementById('cart-total').innerText = `₹${data.subTotal}`;
        document.getElementById('cart-tax').innerText = `₹${data.tax}`;
        document.getElementById('cart-grand-total').innerText = `₹${data.grandTotal}`;
        document.getElementById('coupon-summary').classList.add('hidden');
        document.getElementById('remove-coupon-div').classList.add('hidden');
        document.getElementById('coupon_code').value = '';
    });
});
</script>
</x-app-layout>
