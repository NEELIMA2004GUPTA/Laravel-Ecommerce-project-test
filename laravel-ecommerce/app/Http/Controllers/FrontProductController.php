<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class FrontProductController extends Controller
{
    // Home page
    public function home(){
        $featured=Product::latest()->take(8)->get();
        return view('frontend.home',compact('featured'));
    }

    // All products
    public function products(Request $request){
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();

        $query = Product::query()->with('category');

        // Search by title
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->Where('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('slug', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        // If Category Selected
        if ($request->category) {
            // Get category + all its subcategories
            $categoryIds = Category::where('id', $request->category)
                            ->orWhere('parent_id', $request->category)
                            ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }

        // If Subcategory Selected
        if ($request->subcategory) {
            $query->where('category_id', $request->subcategory);
        }

        // Price Filter
        if ($request->min) $query->where('price', '>=', $request->min);
        if ($request->max) $query->where('price', '<=', $request->max);

        if ($request->sort) {
            if ($request->sort == 'newest') {
                $query->orderBy('created_at', 'desc');
            } 
            elseif ($request->sort == 'oldest') {
                $query->orderBy('created_at', 'asc'); 
            }
        }

        $query->orderBy('created_at', 'desc');
        $products = $query->paginate(12);

        return view('frontend.products.index', compact('categories', 'products'));
    }

    // Product Detail Page
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return view('frontend.products.show', compact('product'));
    }



}
