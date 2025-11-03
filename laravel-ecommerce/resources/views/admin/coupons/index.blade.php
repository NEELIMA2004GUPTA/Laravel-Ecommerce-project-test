<x-app-layout>
<div class="max-w-5xl mx-auto p-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Coupons List</h2>
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
        <a href="{{ route('admin.coupons.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New</a>
    </div>

    <table class="w-full border text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Code</th>
                <th class="p-2 border">Discount</th>
                <th class="p-2 border">Min Amount</th>
                <th class="p-2 border">Expires</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupons as $coupon)
            <tr>
                <td class="p-2 border">{{ $coupon->code }}</td>
                <td class="p-2 border">₹{{ $coupon->discount }}</td>
                <td class="p-2 border">₹{{ $coupon->min_amount }}</td>
                <td class="p-2 border">{{ $coupon->expires_at ?? 'None' }}</td>
                <td class="p-2 border">
                @if($coupon->expires_at && now()->greaterThan($coupon->expires_at))
                    <span class="text-red-600 font-semibold">Expired</span>
                @else
                    @if($coupon->status)
                        <span class="text-green-600 font-semibold">Active</span>
                    @else
                        <span class="text-red-600 font-semibold">Inactive</span>
                    @endif
                @endif
                </td>
                <td class="p-2 border flex gap-2">
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="text-blue-600">Edit</a>
                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-red-600" onclick="return confirm('Delete this coupon?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
</x-app-layout>
