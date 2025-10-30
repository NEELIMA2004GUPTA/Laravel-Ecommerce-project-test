<x-app-layout>
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-2xl font-semibold mb-4">Create Coupon</h2>
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

    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf

        <label class="block mb-2 font-medium">Coupon Code</label>
        <input type="text" name="code" class="w-full border p-2 rounded mb-4" required>

        <label class="block mb-2 font-medium">Discount Amount (₹)</label>
        <input type="number" name="discount" class="w-full border p-2 rounded mb-4" required>

        <label class="block mb-2 font-medium">Minimum Order Amount (₹)</label>
        <input type="number" name="min_amount" class="w-full border p-2 rounded mb-4" required>

        <label class="block mb-2 font-medium">Expiry Date</label>
        <input type="date" name="expires_at" class="w-full border p-2 rounded mb-4">

        <label class="flex items-center gap-2 mb-4">
            <input type="checkbox" name="status" checked>
            <span>Active</span>
        </label>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Create Coupon
        </button>
    </form>

</div>
</x-app-layout>
