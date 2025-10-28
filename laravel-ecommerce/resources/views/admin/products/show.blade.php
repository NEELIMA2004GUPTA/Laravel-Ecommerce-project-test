<x-app-layout>
<div class="container mx-auto p-6">
    <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
        ← Back to Products
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white shadow rounded-lg p-6">
        
        <!-- Product Images Section -->
       <div>
        @php
        $images = is_array($product->images)
            ? $product->images
            : json_decode($product->images ?? '[]', true);
        $count = count($images);
        @endphp

        @if($count === 1)
            {{--  Single Image → Center --}}
            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $images[0]) }}" 
                    alt="{{ $product->title }}"
                    class="rounded-lg shadow-md object-cover w-72 h-72 border">
            </div>

        @elseif($count > 1)
            {{-- Multiple Images → Grid --}}
            <div class="grid grid-cols-2 gap-3">
                @foreach($images as $img)
                    <img src="{{ asset('storage/' . $img) }}" 
                        alt="{{ $product->title }}" 
                        class="rounded-lg shadow-md object-cover w-full h-48 border">
                @endforeach
            </div>

        @else
            {{-- No Images --}}
            <div class="bg-gray-100 text-gray-500 p-10 rounded text-center">
                No Images Available
            </div>
        @endif
    </div>
    
        <!-- Product Details Section -->
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->title }}</h2>
            <p class="text-gray-600 mb-4">{{ $product->description ?? 'No description provided.' }}</p>

            @php
                $discountedPrice = $product->discount > 0
                    ? $product->price - ($product->price * $product->discount / 100)
                    : $product->price;
            @endphp

            <div class="mb-4">
                @if($product->discount > 0)
                    <p class="text-gray-500 line-through">₹{{ number_format($product->price, 2) }}</p>
                    <p class="text-green-600 text-2xl font-semibold">
                        ₹{{ number_format($discountedPrice, 2) }}
                        <span class="text-sm text-red-500 ml-2">({{ $product->discount }}% OFF)</span>
                    </p>
                @else
                    <p class="text-2xl font-semibold text-gray-800">₹{{ number_format($product->price, 2) }}</p>
                @endif
            </div>

            <!-- Variants -->
            @if($product->variants)
                @php
                    $variants = is_array($product->variants)
                        ? $product->variants
                        : json_decode($product->variants ?? '[]', true);
                @endphp
                @if(!empty($variants))
                    <div class="mt-4">
                        <h4 class="font-semibold text-gray-700 mb-1">Variants:</h4>
                        <ul class="list-disc ml-5 text-gray-600">
                            @foreach($variants as $variant)
                                <li>{{ $variant }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif

            <div class="mt-4">
                <h4 class="font-semibold text-gray-700 mb-1">Stock Available:</h4>
                <p class="text-gray-800">{{ $product->stock }} units</p>
            </div>

            <div class="mt-4">
                <h4 class="font-semibold text-gray-700 mb-1">SKU:</h4>
                <p class="text-gray-800">{{ $product->sku }}</p>
            </div>

            <div class="mt-4">
                <h4 class="font-semibold text-gray-700 mb-1">Category:</h4>
                <p class="text-gray-800">{{ $product->category?->name ?? 'Uncategorized' }}</p>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
