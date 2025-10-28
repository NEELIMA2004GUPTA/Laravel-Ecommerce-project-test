<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Add Category</h2>

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Parent Category (optional)</label>
            <select name="parent_id" class="w-full border px-3 py-2 rounded">
                <option value="">None</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" {{ isset($selectedParentId) && $selectedParentId == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        <a href="{{ route('admin.categories.index') }}" class="ml-2 text-gray-600">Cancel</a>
    </form>
</div>
</x-app-layout>
