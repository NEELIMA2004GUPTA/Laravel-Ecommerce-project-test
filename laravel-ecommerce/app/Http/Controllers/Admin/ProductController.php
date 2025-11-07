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
        $search = $request->search ;

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
            'images.*' => 'mimes:jpeg,png,jpg,jfif,webp,,mp4,webm,ogg', 
            'images.*' => [
                function ($attribute, $value, $fail) {
                    $mime = $value->getMimeType();

                    // Image Size Limit: 1MB (1024 KB)
                    if (str_starts_with($mime, 'image/') && $value->getSize() > 1024 * 1024) {
                        return $fail('Each image must be less than 1MB.');
                    }

                    // Video Size Limit: 25MB (25 * 1024 * 1024 bytes)
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
                // Check File Type
                $mime = $file->getMimeType();

                if (str_starts_with($mime, 'image/')) {
                    // Save Images in products/images
                    $path = $file->store('products/images', 'public');
                } 
                elseif (str_starts_with($mime, 'video/')) {
                    // Save Videos in products/videos
                    $path = $file->store('products/videos', 'public');
                } 
                else {
                    continue; // Skip unknown types (should not occur because of validation)
            }
                $imagePaths[] = $path;
            }
        }
        
        $data['images'] = json_encode($imagePaths);

        //  Handle Variants
        if ($request->filled('variants')) {
            $data['variants'] = json_encode($request->variants);
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

    // update
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validation (Images optional)
        $request->validate([
            'category_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'sku' => 'required|string',
            'stock' => 'required|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,jfif,webp|max:4096',
        ]);

        // Update Base Fields
        $product->update([
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'discount'    => $request->discount,
            'sku'         => $request->sku,
            'stock'       => $request->stock,
        ]);

        // Handle Remove Old Images
        $existingImages = is_array($product->images) ? $product->images : json_decode($product->images, true);
        $existingImages = $existingImages ?? [];

        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imgToRemove) {
                // Delete from storage
                if (file_exists(storage_path('app/public/' . $imgToRemove))) {
                    unlink(storage_path('app/public/' . $imgToRemove));
                }
                // Remove from array
                $existingImages = array_filter($existingImages, fn($img) => $img !== $imgToRemove);
            }
        }

        // Handle Upload New Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $existingImages[] = $path;
            }
        }

        // Save final image list
        $product->images = array_values($existingImages);
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product Updated Successfully');
    }

    // ---------------------- DELETE PRODUCT ----------------------
    public function destroy(Product $product)
    {
        if (!empty($product->images)) {

        // Ensure images is always an array
        $images = is_array($product->images)
            ? $product->images
            : json_decode($product->images, true);

        if (is_array($images)) {
            foreach ($images as $img) {
                Storage::disk('public')->delete($img);
            }
        }
    }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', '🗑️ Product deleted!');
    }

    // ---------------------- DELETE SINGLE IMAGE ----------------------
    public function deleteImage(Request $request, Product $product)
    {
        $request->validate(['image' => 'required|string']);

        $images = json_decode($product->images ?? '[]', true);

        if (($key = array_search($request->image, $images)) !== false) {
            Storage::disk('public')->delete($request->image);
            unset($images[$key]);
            $product->images = json_encode(array_values($images));
            $product->save();
        }

        return response()->json(['success' => true]);
    }
}