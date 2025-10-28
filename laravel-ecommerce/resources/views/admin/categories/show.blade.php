<x-app-layout>
<div class="container mx-auto p-6">
    <!-- Page Header -->
    <h2 class="text-2xl font-bold mb-2">
        Manage Subcategories under: 
        <span class="text-blue-600">{{ $category->name }}</span>
    </h2>

    <p class="mb-4 text-gray-600">
        <strong>Parent Category:</strong> {{ $category->name }}
    </p>

    <!-- Add Subcategory Form -->
    <div class="bg-gray-100 p-4 rounded mb-6 shadow-sm">
        <h3 class="font-semibold mb-2">Add a New Subcategory</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="flex items-center space-x-3">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $category->id }}">
            <input 
                type="text" 
                name="name" 
                class="border px-3 py-2 rounded w-1/2 focus:outline-none focus:ring focus:ring-blue-200" 
                placeholder="Enter subcategory name" 
                required
            >
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Add
            </button>
        </form>
    </div>

    <!-- Subcategory List -->
    <div class="bg-white rounded shadow p-4">
        <h3 class="text-xl font-semibold mb-3">Existing Subcategories</h3>

        @if($category->subcategories->isEmpty())
            <p class="text-gray-500">No subcategories yet for <strong>{{ $category->name }}</strong>.</p>
        @else
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border text-left">ID</th>
                        <th class="px-4 py-2 border text-left">Name</th>
                        <th class="px-4 py-2 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->subcategories as $sub)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $sub->id }}</td>
                        <td class="px-4 py-2 border">{{ $sub->name }}</td>
                        <td class="px-4 py-2 border text-center">
                            <a href="{{ route('admin.categories.edit', $sub) }}" class="text-blue-600 hover:underline mr-3">
                                Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $sub) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    onclick="return confirm('Are you sure you want to delete this subcategory?')" 
                                    class="text-red-600 hover:underline"
                                >
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
</x-app-layout>
