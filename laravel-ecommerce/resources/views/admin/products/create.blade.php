<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Add New Product</h2>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block font-medium">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2">
            @error('title')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block font-medium">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Category --}}
        <div>
            <label class="block font-medium">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @foreach ($category->subcategories as $sub)
                        <option value="{{ $sub->id }}" @selected(old('category_id') == $sub->id)">— {{ $sub->name }}</option>
                    @endforeach
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Price & Discount --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Price</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" class="w-full border rounded px-3 py-2">
                @error('price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium">Discount (%)</label>
                <input type="number" name="discount" value="{{ old('discount') }}" step="0.01" class="w-full border rounded px-3 py-2">
                @error('discount')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SKU & Stock --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" class="w-full border rounded px-3 py-2">
                @error('sku')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium">Stock</label>
                <input type="number" name="stock" value="{{ old('stock') }}" class="w-full border rounded px-3 py-2">
                @error('stock')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Variants --}}
        <div>
            <label class="block font-medium">Variants (optional)</label>
            <input type="text" name="variants[]" class="w-full border rounded px-3 py-2 mb-2">
            <input type="text" name="variants[]" class="w-full border rounded px-3 py-2">
        </div>

        {{-- Images --}}
        <div>
            <label class="block font-medium">Product Media (Images or Videos)</label>
            <input type="file" name="images[]" multiple accept="image/*,video/*" class="w-full border rounded px-3 py-2">

            <p>Allowed formats:</p>
            <ul class="list-disc pl-5 text-sm text-gray-600">
                <li>Images: .jpg, .jpeg, .png, .webp, .jfif</li>
                <li>Videos: .mp4, .webm, .ogg</li>
            </ul>

            @error('images.*')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save Product
        </button>
    </form>
</div>
</x-app-layout>
