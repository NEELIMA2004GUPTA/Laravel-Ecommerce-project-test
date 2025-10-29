<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())
                         ->with('product')
                         ->get();

        return view('frontend.wishlist.index', compact('items'));
    }

    public function add(Product $product)
    {
        // Check if product is already in wishlist
        $already = Wishlist::where('user_id', Auth::id())
                           ->where('product_id', $product->id)
                           ->exists();

        if ($already) {
            return redirect()->back()->with('warning', 'Product is already in your wishlist!');
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        return redirect()->back()->with('success', 'Product added to wishlist successfully!');
    }

    public function remove(Product $product)
    {
        Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();

        return back()->with('success', 'Product removed from wishlist!');
    }
}
