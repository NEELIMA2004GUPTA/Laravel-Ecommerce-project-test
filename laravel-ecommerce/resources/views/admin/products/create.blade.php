<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Add New Product</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium">Title</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block font-medium">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div>
            <label class="block font-medium">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @foreach ($category->subcategories as $sub)
                        <option value="{{ $sub->id }}">— {{ $sub->name }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Price</label>
                <input type="number" name="price" step="0.01" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block font-medium">Discount (%)</label>
                <input type="number" name="discount" step="0.01" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">SKU</label>
                <input type="text" name="sku" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block font-medium">Stock</label>
                <input type="number" name="stock" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div>
            <label class="block font-medium">Variants (optional)</label>
            <input type="text" name="variants[]" placeholder="e.g. Size: M, Color: Blue" class="w-full border rounded px-3 py-2 mb-2">
            <input type="text" name="variants[]" placeholder="e.g. Size: L, Color: Red" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium">Product Images</label>
            <input type="file" name="images[]" multiple accept="image/*" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save Product
        </button>
    </form>
</div>
</x-app-layout>
