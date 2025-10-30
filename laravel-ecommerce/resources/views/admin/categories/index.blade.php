<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Categories</h2>
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
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">+ Add Category</a>

    <table class="min-w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Name</th>
                <th class="px-4 py-2 border">Subcategories</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
            <tr>
                <td class="px-4 py-2 border">
                    <a href="{{ route('admin.categories.show', $category) }}" class="text-blue-600 hover:underline">
                        {{ $category->name }}
                    </a>
                </td>
                <td class="px-4 py-2 border">{{ $category->subcategories_count }}</td>
                <td class="px-4 py-2 border">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 mr-2">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Delete this category?')" class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $categories->links() }}</div>
</div>
</x-app-layout>
