<x-app-layout>

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-5">
            @if($product->images)
                @php $images = json_decode($product->images); @endphp
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded">
                        @foreach($images as $key => $image)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 rounded" alt="{{ $product->title }}">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            @else
                <img src="https://via.placeholder.com/400x400?text=No+Image" class="img-fluid rounded">
            @endif
        </div>

        <!-- Product Info -->
        <div class="col-md-7">
            <h2 class="fw-bold">{{ $product->title }}</h2>
            <p class="text-muted mb-1">Category: <strong>{{ $product->category->name ?? 'Uncategorized' }}</strong></p>

            <div class="mt-3">
                <h4 class="text-success fw-bold">₹{{ number_format($product->price, 2) }}</h4>
                @if($product->discount)
                    <span class="badge bg-danger">{{ $product->discount }}% OFF</span>
                @endif
            </div>

            <p class="mt-4">{{ $product->description }}</p>

            <div class="mt-4">
                <p class="mb-1"><strong>Available Stock:</strong> {{ $product->stock }}</p>
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">-</button>
                        <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock }}">
                        <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">+</button>
                    </div>
                    <button class="btn btn-primary mt-3"><i class="bi bi-cart"></i> Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <!-- Related Products -->
    <div class="mt-5">
        <h4>Related Products</h4>
        <div class="row mt-3">
            @foreach(App\Models\Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->take(4)->get() as $related)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/' . json_decode($related->images)[0]) }}" class="card-img-top" height="180" style="object-fit:cover;">
                        <div class="card-body text-center">
                            <h6 class="fw-bold">{{ $related->title }}</h6>
                            <p class="text-success mb-0">₹{{ number_format($related->price, 2) }}</p>
                            <a href="{{ route('product.show', $related->slug) }}" class="btn btn-sm btn-outline-primary mt-2">View</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function decreaseQty() {
    let qty = document.getElementById('quantity');
    if (qty.value > 1) qty.value--;
}
function increaseQty() {
    let qty = document.getElementById('quantity');
    qty.value++;
}
</script>
@endsection

</x-app-layout>
