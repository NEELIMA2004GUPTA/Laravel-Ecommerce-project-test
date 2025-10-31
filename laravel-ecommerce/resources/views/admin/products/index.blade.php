<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Products</h2>
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

    <a href="{{ route('admin.products.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        + Add Product
    </a>

    <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search products..."
               class="border px-3 py-2 rounded-lg w-72 focus:ring-blue-500 focus:border-blue-500">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Search
        </button>
    </form><br>

    <table class="min-w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border text-left">Title</th>
                <th class="px-4 py-2 border text-left">Price</th>
                <th class="px-4 py-2 border text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border font-medium text-gray-800">{{ $product->title }}</td>
                <td class="px-4 py-2 border">
                    ₹{{ number_format($product->price, 2) }}
                </td>
                <td class="px-4 py-2 border text-center space-x-2">
                    <a href="{{ route('admin.products.show', $product->id) }}" 
                       class="text-blue-600 hover:underline">View</a>
                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                       class="text-yellow-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                          method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Delete this product?')" 
                                class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $products->links() }}</div>
</div>
</x-app-layout>
