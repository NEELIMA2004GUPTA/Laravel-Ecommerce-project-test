<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Products</h2>

    <a href="{{ route('admin.products.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        + Add Product
    </a>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

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
