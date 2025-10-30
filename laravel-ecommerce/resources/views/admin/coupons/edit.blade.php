<x-app-layout>
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">

    <h2 class="text-2xl font-semibold mb-6">Edit Coupon</h2>
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

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Code -->
        <label class="block mb-2 font-medium">Coupon Code</label>
        <input type="text" name="code" value="{{ $coupon->code }}"
               class="w-full border p-2 rounded mb-4" required>

        <!-- Discount -->
        <label class="block mb-2 font-medium">Discount Amount (₹)</label>
        <input type="number" name="discount" value="{{ $coupon->discount }}"
               class="w-full border p-2 rounded mb-4" required>

        <!-- Minimum Order -->
        <label class="block mb-2 font-medium">Minimum Order Amount (₹)</label>
        <input type="number" name="min_amount" value="{{ $coupon->min_amount }}"
               class="w-full border p-2 rounded mb-4" required>

        <!-- Expiry -->
        <label class="block mb-2 font-medium">Expiry Date</label>
        <input type="date" name="expires_at" value="{{ $coupon->expires_at }}"
               class="w-full border p-2 rounded mb-4">

        <!-- Status -->
        <label class="flex items-center gap-2 mb-6">
            <input type="checkbox" name="status" {{ $coupon->status ? 'checked' : '' }}>
            <span>Active</span>
        </label>

        <!-- Update Button -->
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-medium">
            Update Coupon
        </button>

    </form>

</div>
</x-app-layout>
