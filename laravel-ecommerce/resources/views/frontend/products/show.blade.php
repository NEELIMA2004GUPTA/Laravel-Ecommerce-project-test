<x-app-layout>
<div class="max-w-6xl mx-auto py-10 px-4">

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">{{ session('error') }}</div>
    @endif

    <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
        ← Back to Shop
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white rounded-lg shadow p-6">

        {{-- Product Images --}}
        @php
            $images = is_array($product->images) ? $product->images : json_decode($product->images ?? '[]', true);
            $defaultImage = count($images) ? asset('storage/' . $images[0]) : 'https://via.placeholder.com/400';
        @endphp

        <div x-data="{ activeImage: '{{ $defaultImage }}' }">
            <img :src="activeImage" class="w-full h-96 object-cover rounded-lg shadow transition duration-200">

            <div class="flex gap-3 mt-4 overflow-x-auto">
                @foreach($images as $img)
                    @php $thumb = asset('storage/' . $img); @endphp
                    <img src="{{ $thumb }}" @click="activeImage = '{{ $thumb }}'"
                        class="w-20 h-20 object-cover rounded border cursor-pointer hover:opacity-80 transition"
                        :class="activeImage === '{{ $thumb }}' ? 'ring-2 ring-blue-500' : ''">
                @endforeach
            </div>
        </div>

        {{-- Product Info --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-800">{{ $product->title }}</h2>
            <p class="text-gray-600 mt-2">{{ $product->description ?? 'No description available.' }}</p>

            @php
                $discounted = $product->discount ? $product->price - ($product->price * $product->discount / 100) : $product->price;
            @endphp

            <div class="mt-4">
                @if($product->discount)
                    <p class="line-through text-gray-400">₹{{ number_format($product->price, 2) }}</p>
                    <p class="text-3xl font-semibold text-green-600">
                        ₹{{ number_format($discounted, 2) }}
                        <span class="text-sm text-red-500">({{ $product->discount }}% OFF)</span>
                    </p>
                @else
                    <p class="text-3xl font-semibold text-gray-800">₹{{ number_format($product->price, 2) }}</p>
                @endif
            </div>

            {{-- Ratings --}}
            <div class="mt-4 flex items-center gap-2">
                <span class="text-lg font-semibold">{{ $product->averageRating() }}/5</span>
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($product->averageRating()) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.945a1 1 0 00.95.69h4.157c.969 0 1.371 1.24.588 1.81l-3.368 2.449a1 1 0 00-.364 1.118l1.286 3.945c.3.921-.755 1.688-1.538 1.118l-3.368-2.449a1 1 0 00-1.176 0l-3.368 2.449c-.783.57-1.838-.197-1.538-1.118l1.286-3.945a1 1 0 00-.364-1.118L2.22 9.372c-.783-.57-.38-1.81.588-1.81h4.157a1 1 0 00.95-.69l1.286-3.945z" />
                        </svg>
                    @endfor
                </div>
                <span class="text-sm text-gray-600">({{ $product->ratingCount() }} reviews)</span>
            </div>

            {{-- Add Review Button --}}
            @auth
                <a href="{{ route('products.reviews.create', $product) }}"
                   class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    Add Review
                </a>
            @else
                <p class="mt-3 text-sm">Please <a class="text-blue-600" href="{{ route('login') }}">login</a> to add a review.</p>
            @endauth

            {{-- Add to Cart --}}
            <div class="mt-6">
                @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product) }}" method="POST">@csrf
                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Add to Cart</button>
                    </form>
                @else
                    <span class="px-4 py-2 bg-red-600 text-white rounded">Out of Stock</span>
                @endif
            </div>

            {{-- Wishlist --}}
            <form action="{{ route('wishlist.add', $product) }}" method="POST" class="mt-3">@csrf
                <button class="px-4 py-2 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition">
                    ❤️ Add to Wishlist
                </button>
            </form>

        </div>

    </div>

    {{-- Reviews --}}
    <div class="mt-10">
        <h3 class="text-xl font-semibold mb-4">Customer Reviews</h3>

        @forelse($product->reviews()->latest()->get() as $review)
            <div class="border p-4 rounded mb-4">
                <strong>{{ $review->user->name }}</strong>
                <span class="text-sm text-gray-500">• {{ $review->created_at->diffForHumans() }}</span>
                {{-- Star Rating --}}
                <div class="flex items-center gap-1 mt-1">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.945a1 1 0 00.95.69h4.157c.969 0 1.371 1.24.588 1.81l-3.368 2.449a1 1 0 00-.364 1.118l1.286 3.945c.3.921-.755 1.688-1.538 1.118l-3.368-2.449a1 1 0 00-1.176 0l-3.368 2.449c-.783.57-1.838-.197-1.538-1.118l1.286-3.945a1 1 0 00-.364-1.118L2.22 9.372c-.783-.57-.38-1.81.588-1.81h4.157a1 1 0 00.95-.69l1.286-3.945z" />
                    </svg>
                @endfor
                </div>
                <p class="mt-2">{{ $review->comment }}</p>

                @if($review->media)
                <div class="flex gap-3 mt-2">
                    @foreach($review->media as $m)
                        @if($m->type === 'image')
                            <img src="{{ Storage::url($m->path) }}" class="w-24 h-24 rounded object-cover">
                        @else
                            <video controls class="w-64 h-40"><source src="{{ Storage::url($m->path) }}"></video>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        @empty
            <p class="text-gray-600">No reviews yet.</p>
        @endforelse
    </div>

</div>


<!-- ALPINE.JS FOR INTERACTIVITY -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</x-app-layout>
