<x-app-layout>

<div class="max-w-3xl mx-auto bg-white shadow p-6 rounded">
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4">Edit Product</h2>
    

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label class="font-semibold">Category</label>
        <select name="category_id" class="w-full border p-2 mb-3" required>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @foreach($cat->subcategories as $sub)
                    <option value="{{ $sub->id }}" {{ $product->category_id == $sub->id ? 'selected' : '' }}>
                        — {{ $sub->name }}
                    </option>
                @endforeach
            @endforeach
        </select>

        <label class="font-semibold">Title</label>
        <input type="text" name="title" value="{{ $product->title }}" class="w-full border p-2 mb-3" required>

        <label class="font-semibold">Description</label>
        <textarea name="description" class="w-full border p-2 mb-3">{{ $product->description }}</textarea>

        <label class="font-semibold">Price</label>
        <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="w-full border p-2 mb-3" required>

        <label class="font-semibold">Discount</label>
        <input type="number" step="0.01" name="discount" value="{{ $product->discount }}" class="w-full border p-2 mb-3">

        <label class="font-semibold">SKU</label>
        <input type="text" name="sku" value="{{ $product->sku }}" class="w-full border p-2 mb-3" required>

        <label class="font-semibold">Stock</label>
        <input type="number" name="stock" value="{{ $product->stock }}" class="w-full border p-2 mb-3" required>

        <label class="font-semibold mb-1">Existing Images</label>
        <div class="flex flex-wrap gap-3 mb-4">
        @php
            $imgs = is_array($product->images) ? $product->images : json_decode($product->images, true);
        @endphp

        @foreach($imgs ?? [] as $img)
        <div class="relative inline-block">
            <img src="{{ asset('storage/' . $img) }}" class="w-24 h-24 object-cover rounded border shadow">
            <button type="button"
                class="remove-image-btn absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 text-sm"
                data-image="{{ $img }}">x</button>
        </div>
        @endforeach
    </div>

        <label class="font-semibold">Add More Images</label>
        <input type="file" name="images[]" multiple class="w-full border p-2 mb-4 form-control">

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>

</div>

<script>
document.querySelectorAll('.remove-image-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const img = this.dataset.image;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_images[]';
        input.value = img;
        document.querySelector('form').appendChild(input);

        this.parentElement.remove();
    });
});
</script>

</x-app-layout>