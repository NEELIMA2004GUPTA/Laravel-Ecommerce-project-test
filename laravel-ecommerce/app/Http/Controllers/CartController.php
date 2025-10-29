<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index(){
        $cart=session()->get('cart',[]);
        return view('frontend.cart.index',compact('cart'));
    }

    public function add(Product $product){
        $cart=session()->get('cart',[]);

        $discountedPrice = $product->discount > 0
        ? $product->price - ($product->price * $product->discount / 100)
        : $product->price;

        if(isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } 
        
        else {
            $cart[$product->id] = [
                'title'        => $product->title,
                'original_price' => $product->price,
                'price'        => $discountedPrice,
                'discount'     => $product->discount,
                'qty'          => 1
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    public function update(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        $cart[$product->id]['qty'] = $request->qty;

        session()->put('cart', $cart);
        return back()->with('success', 'Cart Updated!');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Item removed!');
    }
}
