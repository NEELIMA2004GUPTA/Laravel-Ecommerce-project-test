<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-6">My Wishlist</h2>
        <a href="{{ route('products.index') }}"
            class="text-blue-600 hover:text-blue-800 hover:underline text-sm">
                ← Back to Products
        </a><br><br>

        @if($items->count() > 0)
            <div class="space-y-4">

                @foreach($items as $item)
                    @php
                       $imgs = is_array($item->product->images)
                            ? $item->product->images
                            : json_decode($item->product->images ?? '[]', true);
                            $firstImage = $imgs[0] ?? null;
                            
                        $price = $item->product->price;
                        $discount = $item->product->discount; // percentage
                        $discounted = $discount > 0 ? $price - ($price * $discount / 100) : $price;
                    @endphp

                    <div class="flex items-center bg-white border rounded-lg shadow-sm p-4 hover:shadow-md transition">

                        <!-- Image -->
                        <img src="{{ $firstImage ? asset('storage/'.$firstImage) : asset('no-image.jpg') }}"
                             class="w-24 h-24 rounded-md object-cover">

                        <!-- Product Details -->
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $item->product->title }}</h3>

                            @if($discount > 0)
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-xl font-bold text-red-600">₹{{ number_format($discounted, 2) }}</span>
                                    <span class="text-gray-500 line-through">₹{{ number_format($price, 2) }}</span>
                                    <span class="text-green-600 text-sm font-semibold">{{ $discount }}% OFF</span>
                                </div>
                            @else
                                <p class="text-lg font-semibold text-gray-900 mt-1">₹{{ number_format($price, 2) }}</p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="mt-4 flex items-center space-x-3">

                                <!-- Add to Cart -->
                                <form action="{{ route('cart.add', $item->product->id) }}" method="POST">
                                    @csrf
                                    <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                                        Add to Cart
                                    </button>
                                </form>

                                <!-- View Product -->
                                <a href="{{ route('product.show', $item->product->slug) }}"
                                   class="px-4 py-2 border border-gray-400 text-gray-700 text-sm rounded-lg hover:bg-gray-100">
                                    View Product
                                </a>

                                <!-- Remove -->
                                <form action="{{ route('wishlist.remove', $item->product->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">
                                        Remove
                                    </button>
                                </form>

                            </div>
                        </div>

                    </div>

                @endforeach

            </div>

        @else
            <p class="text-center text-gray-500 py-10">No items in your wishlist.</p>
        @endif
    </div>
</x-app-layout>
