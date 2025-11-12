<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(){
        $cart = session()->get('cart', []);
        return view('frontend.cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        $qtyToAdd = (int) ($request->quantity ?? 1);

        if (isset($cart[$product->id])) {
            $newQty = $cart[$product->id]['quantity'] + $qtyToAdd;
            $cart[$product->id]['quantity'] = min($newQty, $product->stock);
        } else {
            $discountedPrice = $product->discount > 0
                ? $product->price - ($product->price * $product->discount / 100)
                : $product->price;

            $cart[$product->id] = [
                'title' => $product->title,
                'original_price' => $product->price,
                'price' => $discountedPrice,
                'discount' => $product->discount,
                'quantity' => min($qtyToAdd, $product->stock),
                'stock' => $product->stock,
            ];
        }

        session()->put('cart', $cart);

        $msg = Auth::check() ? 'Product added to your cart.' : 'Product added to cart. Please login to see your cart.';
        return redirect()->back()->with('success', $msg);
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $maxStock = $cart[$id]['stock'];
            $qty = max(1, min($request->qty, $maxStock));
            $cart[$id]['quantity'] = $qty;

            session()->put('cart', $cart);

            $total = 0;
            $items = 0;

            foreach($cart as $item){
                $total += $item['price'] * $item['quantity'];
                $items += $item['quantity'];
            }

            // Apply coupon if any
            $discountAmount = session('coupon.discount') ?? 0;
            $subTotal = $total - $discountAmount;
            $tax = $subTotal * 0.05; // 5% GST
            $grandTotal = $subTotal + $tax;

            return response()->json([
                'success' => true,
                'qty' => $qty,
                'subtotal' => number_format($cart[$id]['price'] * $qty, 2),
                'total' => number_format($subTotal, 2),
                'tax' => number_format($tax, 2),
                'grandTotal' => number_format($grandTotal, 2),
                'totalItems' => $items
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        $total = 0;
        $items = 0;
        foreach($cart as $item){
            $total += $item['price'] * $item['quantity'];
            $items += $item['quantity'];
        }

        $discountAmount = session('coupon.discount') ?? 0;
        $subTotal = $total - $discountAmount;
        $tax = $subTotal * 0.05;
        $grandTotal = $subTotal + $tax;

        return response()->json([
            'success' => true,
            'message' => 'Item removed!',
            'total' => number_format($subTotal,2),
            'tax' => number_format($tax,2),
            'grandTotal' => number_format($grandTotal,2),
            'totalItems' => $items
        ]);
    }
}
