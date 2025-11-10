<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ---------------------- INDEX ----------------------
    public function index(Request $request)
    {
        $search = $request->search;

        $products = Product::with('category')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('slug', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.products.index', compact('products'));
    }

    // ---------------------- CREATE ----------------------
    public function create()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        return view('admin.products.create', compact('categories'));
    }

    // ---------------------- STORE ----------------------
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku',
            'stock' => 'required|integer|min:0',
            'variants' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'mimes:jpeg,png,jpg,jfif,webp,mp4,webm,ogg',
            'images.*' => [
                function ($attribute, $value, $fail) {
                    $mime = $value->getMimeType();

                    // Image Size Limit: 5MB 
                    if (str_starts_with($mime, 'image/') && $value->getSize() > 5 * 1024 * 1024) {
                        return $fail('Each image must be less than 5MB.');
                    }

                    // Video Size Limit: 25MB
                    if (str_starts_with($mime, 'video/') && $value->getSize() > 25 * 1024 * 1024) {
                        return $fail('Each video must be less than 25MB.');
                    }
                }
            ]
        ]);

        $data = $request->only(['title', 'description', 'category_id', 'price', 'discount', 'sku', 'stock']);

        // Handle Multiple Images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $mime = $file->getMimeType();
                if (str_starts_with($mime, 'image/')) {
                    $path = $file->store('products/images', 'public');
                } elseif (str_starts_with($mime, 'video/')) {
                    $path = $file->store('products/videos', 'public');
                } else {
                    continue;
                }
                $imagePaths[] = $path;
            }
        }
        $data['images'] = $imagePaths;

        // Handle Variants
        if ($request->filled('variants')) {
            $data['variants'] = $request->variants;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully!');
    }

    // ---------------------- SHOW ----------------------
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    // ---------------------- EDIT ----------------------
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // ---------------------- UPDATE ----------------------
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'sku' => 'required|string',
            'stock' => 'required|integer',
            'images.*' => 'nullable|mimes:jpeg,png,jpg,jfif,webp,mp4,webm,ogg|max:4096',
        ]);

        $product->update($request->only([
            'category_id', 'title', 'description', 'price', 'discount', 'sku', 'stock'
        ]));

        // Handle remove old images
        $existingImages = $product->images ?? [];

        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imgToRemove) {
                Storage::disk('public')->delete($imgToRemove);
                $existingImages = array_filter($existingImages, fn($img) => $img !== $imgToRemove);
            }
        }

        // Handle upload new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $existingImages[] = $path;
            }
        }

        $product->images = array_values($existingImages);
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    // ---------------------- DELETE PRODUCT ----------------------
    public function destroy(Product $product)
    {
        $images = $product->images ?? [];
        foreach ($images as $img) {
            Storage::disk('public')->delete($img);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', '🗑️ Product deleted!');
    }

    // ---------------------- DELETE SINGLE IMAGE ----------------------
    public function deleteImage(Request $request, Product $product)
    {
        $request->validate(['image' => 'required|string']);

        $images = $product->images ?? [];

        if (($key = array_search($request->image, $images)) !== false) {
            Storage::disk('public')->delete($request->image);
            unset($images[$key]);
            $product->images = array_values($images);
            $product->save();
        }

        return response()->json(['success' => true]);
    }
}
