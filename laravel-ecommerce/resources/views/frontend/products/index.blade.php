<x-app-layout>
@php
    function isImageFile($file) {
        return preg_match('/\.(jpg|jpeg|png|webp|jfif)$/i', $file);
    }
    function isVideoFile($file) {
        return preg_match('/\.(mp4|webm|ogg)$/i', $file);
    }
@endphp
    
<div class="max-w-7xl mx-auto py-10 px-4" x-data="{ openFilters: false }">

    <!-- Page Title + Search -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Shop</h1>

        <form method="GET" action="{{ route('products.index') }}" class="flex">
            <input type="text" name="search" placeholder="Search products..."
                   value="{{ request('search') }}"
                   class="border border-gray-300 rounded-l-lg px-3 py-2 w-56 focus:ring-blue-500 focus:border-blue-500">
            <button class="bg-blue-600 text-white px-4 rounded-r-lg hover:bg-blue-700 transition">
                Search
            </button>
        </form>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        <!-- FILTER SIDEBAR -->
        <aside class="bg-white p-5 rounded-lg shadow h-fit hidden md:block">
            <h2 class="font-semibold text-lg mb-4 border-b pb-2">Filters</h2>

            <!-- Category Filter -->
            <h3 class="font-medium text-gray-700 mb-2">Category</h3>
            <select name="category" class="w-full border rounded p-2"
                onchange="this.form.submit()"
                form="filterForm">
                <option value="">All</option>

                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>

                    @foreach($cat->subcategories as $sub)
                        <option value="{{ $sub->id }}" {{ request('category') == $sub->id ? 'selected' : '' }}>
                            — {{ $sub->name }}
                        </option>
                    @endforeach
                @endforeach
            </select>

            <!-- Price Filter -->
            <h3 class="font-medium text-gray-700 mt-6 mb-2">Price (₹)</h3>
            <form id="filterForm" method="GET" class="space-y-3">
                <input type="number" name="min" placeholder="Min"
                       value="{{ request('min') }}"
                       class="w-full border rounded p-2">
                <input type="number" name="max" placeholder="Max"
                       value="{{ request('max') }}"
                       class="w-full border rounded p-2">
                
                <select name="sort" onchange="this.form.submit()">
                    <option value="">Sort By</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                </select>

                <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    Apply
                </button>
            </form>
        </aside>



        <!-- PRODUCTS LIST -->
        <main class="md:col-span-3">
            @if($products->count() == 0)
                <p class="text-gray-500 text-center text-lg mt-10">No products found.</p>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @php
                        $imgArray = is_array($product->images) ? $product->images : json_decode($product->images, true);
                        $img = $imgArray[0] ?? null;
                    @endphp

                    <a href="{{ route('product.show', $product->slug) }}"
                        class="group bg-white rounded-lg shadow hover:shadow-xl hover:-translate-y-1 transition-all duration-300 block">

                        <div class="relative">
                            @if($img)
                                @if(isImageFile($img))
                                    <img src="{{ asset('storage/' . $img) }}"
                                    class="h-56 w-full object-cover rounded-t-lg">
                                @elseif(isVideoFile($img))
                                    <video src="{{ asset('storage/' . $img) }}" 
                                    controls 
                                    class="h-56 w-full object-cover rounded-t-lg"></video>
                                @endif
                            @else
                                <div class="h-56 w-full bg-gray-200 rounded-t-lg flex items-center justify-center text-gray-500">
                                    No Media
                            </div>
                            @endif

                            @if($product->discount > 0)
                                <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">
                                    -{{ $product->discount }}%
                                </span>
                            @endif
                        </div>

                        <div class="p-3">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $product->title }}</h3>
                            <p class="text-gray-500 text-sm">{{ $product->category->name }}</p>

                            <div class="mt-2">
                                @if($product->discount > 0)
                                    <p class="text-red-600 font-bold text-lg">₹{{ $product->price - ($product->price * $product->discount / 100) }}</p>
                                    <p class="line-through text-gray-400 text-sm">₹{{ $product->price }}</p>
                                @else
                                    <p class="text-blue-600 font-bold text-lg">₹{{ $product->price }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout>
