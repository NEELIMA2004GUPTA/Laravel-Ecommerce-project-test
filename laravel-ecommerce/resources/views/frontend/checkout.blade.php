<x-app-layout>
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
<form action="{{ route('place.order') }}" method="POST" class="space-y-5 bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
@csrf

<h2 class="text-2xl font-semibold text-gray-800 mb-4">Checkout Details</h2>
<div class="max-w-2xl mx-auto mb-4">
    <a href="{{ route('cart.index') }}"
        class="inline-block text-blue-600 hover:text-blue-800 hover:underline text-sm">
           ← Back to Cart
    </a>
</div>

<div class="space-y-2">
    <label class="block text-gray-600 text-sm font-medium">Full Name</label>
    <input type="text" name="name" value="{{ old('name') }}" required
           class="w-full border rounded-lg px-4 py-2 focus:ring-blue-200 @error('name') border-red-500 @enderror">
    @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="space-y-2">
    <label class="block text-gray-600 text-sm font-medium">Phone</label>
    <div class="flex gap-2">
        <select name="country_code" class="border rounded-lg px-3 py-2 w-64">
            <option value="+91" selected>+91 🇮🇳</option>
            <option value="+1">+1 🇺🇸</option>
            <option value="+44">+44 🇬🇧</option>
        </select>

    <input type="text" name="phone" value="{{ old('phone') }}" required
           class="w-full border rounded-lg px-4 py-2 focus:ring-blue-200 @error('phone') border-red-500 @enderror">
    @error('phone') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="space-y-2">
    <label class="block text-gray-600 text-sm font-medium">Pincode</label>
    <input type="text" id="pincode" name="pincode" maxlength="6" required
           class="w-full border rounded-lg px-4 py-2">
    <p id="pincode_msg" class="text-sm mt-1"></p>
</div>

<div class="space-y-2 mt-3">
    <label class="block text-gray-600 text-sm font-medium">Full Address</label>
    <textarea name="address" id="address" required rows="3"
              class="w-full border rounded-lg px-4 py-2 focus:ring-blue-200 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
    @error('address') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="space-y-2">
    <label class="block text-gray-600 text-sm font-medium">Payment Method</label>
    <select name="payment_method"
            class="w-full border rounded-lg px-4 py-2 focus:ring-blue-200 @error('payment_method') border-red-500 @enderror">
        <option value="COD" {{ old('payment_method')=='COD'?'selected':'' }}>Cash on Delivery</option>
        <option value="UPI" {{ old('payment_method')=='UPI'?'selected':'' }}>UPI</option>
        <option value="Card" {{ old('payment_method')=='Card'?'selected':'' }}>Card</option>
    </select>
    @error('payment_method') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="space-y-2">
    <label class="block text-gray-600 text-sm font-medium">Notes (optional)</label>
    <textarea name="notes" rows="2"
              class="w-full border rounded-lg px-4 py-2 focus:ring-blue-200 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
    @error('notes') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<button class="w-full bg-green-600 hover:bg-green-700 text-white text-lg font-semibold py-3 rounded-lg transition">
    Place Order
</button>

</form>
<script>
document.getElementById('pincode').addEventListener('blur', function() {
    let pincode = this.value;

    if(pincode.length === 6){
        fetch(`https://api.postalpincode.in/pincode/${pincode}`)
        .then(response => response.json())
        .then(data => {
            if(data[0].Status === "Success"){
                let post = data[0].PostOffice[0];
                let autoAddress = `${post.Name}, ${post.District}, ${post.State}`;
                
                document.getElementById('address').value = autoAddress;
                document.getElementById('pincode_msg').innerHTML = "<span class='text-green-600'> Address auto-filled</span>";
            } else {
                document.getElementById('pincode_msg').innerHTML = "<span class='text-red-600'> Invalid pincode</span>";
            }
        });
    } else {
        document.getElementById('pincode_msg').innerHTML = "<span class='text-red-600'>Pincode must be 6 digits</span>";
    }
});
</script>

</x-app-layout>
