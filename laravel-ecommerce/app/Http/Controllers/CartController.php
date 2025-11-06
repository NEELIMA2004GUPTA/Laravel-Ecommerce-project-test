<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(){
        $cart=session()->get('cart',[]);
        return view('frontend.cart.index',compact('cart'));
    }

    public function add(Request $request, Product $product)
{
    $cart = session()->get('cart', []);

    // Get quantity from request or default to 1
    $qtyToAdd = (int) $request->quantity ?? 1;

    if (isset($cart[$product->id])) {
        // Increase quantity if product already in cart
        $newQty = $cart[$product->id]['quantity'] + $qtyToAdd;

        // Stock check
        if ($newQty > $product->stock) {
            $newQty = $product->stock;
        }

        $cart[$product->id]['quantity'] = $newQty;
    } else {
        // Stock check for first addition
        if ($qtyToAdd > $product->stock) {
            $qtyToAdd = $product->stock;
        }

        $discountedPrice = $product->discount > 0
            ? $product->price - ($product->price * $product->discount / 100)
            : $product->price;

        $cart[$product->id] = [
            'title' => $product->title,
            'original_price' => $product->price,
            'price' => $discountedPrice,
            'discount' => $product->discount,
            'quantity' => $qtyToAdd,
            'stock' => $product->stock,
        ];
    }

    session()->put('cart', $cart);

    if (Auth::check()) {
        return redirect()->back()->with('success', 'Product added to your cart.');
    } else {
        return redirect()->back()->with('success', 'Product added to cart. Please login to see your cart.');
    }
}


    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $maxStock = $cart[$id]['stock'];

            $qty = min($request->qty, $maxStock); 
            $cart[$id]['qty'] = $qty;

            session()->put('cart', $cart);
    }

    return back()->with('success', 'Cart updated!');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Item removed!');
    }
}
