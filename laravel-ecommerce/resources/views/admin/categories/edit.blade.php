<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Category</h2>
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

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Parent Category (optional)</label>
            <select name="parent_id" class="w-full border px-3 py-2 rounded">
                <option value="">None</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" @selected($parent->id == $category->parent_id)>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
        <a href="{{ route('admin.categories.index') }}" class="ml-2 text-gray-600">Cancel</a>
    </form>
</div>
</x-app-layout>
