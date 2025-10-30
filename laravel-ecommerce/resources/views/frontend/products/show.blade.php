<x-app-layout>
<div class="container mx-auto p-6">
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

    <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
        ← Back to Shop
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white shadow rounded-lg p-6">

        <!-- Product Images Section -->
        @php
            $images = is_array($product->images)
                ? $product->images
                : json_decode($product->images ?? '[]', true);

            $activeImage = count($images) ? asset('storage/' . $images[0]) : null;
        @endphp

        <div x-data="{ activeImage: '{{ $activeImage }}' }" class="flex flex-col items-center">
    
            <!-- Main Display Image -->
            <img :src="activeImage" 
                class="rounded-lg shadow-md object-cover w-96 h-96 border hover:scale-105 transition-transform duration-200">

            <!-- Thumbnails -->
            <div class="flex gap-3 mt-4 justify-center">
                @foreach($images as $img)
                    @php 
                        $thumb = asset('storage/' . $img); 
                    @endphp
                    <img src="{{ $thumb }}"
                        @click="activeImage = '{{ $thumb }}'"
                            class="w-20 h-20 rounded border object-cover cursor-pointer hover:opacity-80 transition"
                            :class="activeImage === '{{ $thumb }}' ? 'ring-2 ring-blue-500' : ''">
                @endforeach
    </div>
</div>
        <!-- Product Details Section -->
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->title }}</h2>
            <p class="text-gray-600 mb-4">{{ $product->description ?? 'No description available.' }}</p>

            @php
                $discountedPrice = $product->discount > 0
                    ? $product->price - ($product->price * $product->discount / 100)
                    : $product->price;
            @endphp

            <div class="mb-4">
                @if($product->discount > 0)
                    <p class="text-gray-400 line-through">₹{{ number_format($product->price, 2) }}</p>
                    <p class="text-green-600 text-3xl font-semibold">
                        ₹{{ number_format($discountedPrice, 2) }}
                        <span class="text-sm text-red-500 ml-1">({{ $product->discount }}% OFF)</span>
                    </p>
                @else
                    <p class="text-3xl font-semibold text-gray-800">₹{{ number_format($product->price, 2) }}</p>
                @endif
            </div>

            <!-- Variants -->
            @php
                $variants = json_decode($product->variants ?? '[]', true);
            @endphp

            @if(!empty($variants))
            <div x-data="{ selectedVariant: '{{ $variants[0] ?? '' }}' }" class="mb-6">
                <h4 class="font-semibold text-gray-700 mb-2">Select Variant:</h4>
                <div class="flex gap-2">
                    @foreach($variants as $variant)
                        <button @click="selectedVariant = '{{ $variant }}'"
                                :class="selectedVariant === '{{ $variant }}' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                class="px-3 py-1 rounded border hover:bg-blue-500 hover:text-white transition">
                            {{ $variant }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <p class="text-gray-800 mb-2"><strong>Stock:</strong> {{ $product->stock }} units</p>
            <p class="text-gray-800 mb-4"><strong>SKU:</strong> {{ $product->sku }}</p>

            <div class="flex items-center gap-3 mt-3">

            <!-- {{-- Add to Cart / Out of Stock --}} -->
            @if($product->stock > 0)
            <form action="{{ route('cart.add', $product->id) }}" method="POST">
            @csrf
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                Add to Cart
            </button>
            </form>

            @else
            <span class="px-4 py-2 rounded-lg bg-red-600 text-white font-medium">
                Out of Stock
            </span>
            @endif


            <!-- {{-- Wishlist Button --}} -->
            <form action="{{ route('wishlist.add', $product->id) }}" method="POST">
            @csrf
            <button type="submit"
            class="px-4 py-2 rounded-lg border border-red-600 text-red-600 font-medium hover:bg-red-600 hover:text-white transition flex items-center gap-1">
                ❤️ Wishlist
            </button>
            </form>

        </div>
            
    </div>
</div>

<!-- ALPINE.JS FOR INTERACTIVITY -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</x-app-layout>
