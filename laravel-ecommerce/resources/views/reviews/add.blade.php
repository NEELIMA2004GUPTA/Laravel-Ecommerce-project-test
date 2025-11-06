<x-app-layout>
<div class="max-w-3xl mx-auto py-6">

    <h2 class="text-2xl font-bold mb-4">Add Review for: {{ $product->name }}</h2>

    @include('reviews._form', ['product' => $product])

</div>
</x-app-layout>

