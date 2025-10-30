<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Coupon;

class CheckoutController extends Controller
{
    public function index(){
        $cart = session()->get('cart', []); 

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }

        return view('frontend.checkout', compact('cart'));
    }

    public function placeOrder(Request $request){
        $request->validate([
            'name' => 'required',
            'phone' => 'required|digits:10',
            'address' => 'required',
            'payment_method' => 'required'
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }

        $total = array_reduce($cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['qty']);
        }, 0);

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes ?? null,
            'total' => $total,
        ]);

        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['qty'],
                'price' => $item['price'],
            ]);

        // Reduce stock
        Product::where('id', $productId)->decrement('stock', $item['qty']);
        }

        // Empty cart session
        session()->forget('cart');

        return redirect()->route('orders')->with('success', 'Order placed successfully!');
    

    }

    public function cancelOrder(Order $order)
    {
        // ensure the logged-in user owns the order
        if ($order->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // allow cancellation only if Pending or Shipped
        if (!in_array($order->status, ['Pending', 'Shipped'])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        // Return stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        // Update status
        $order->status = 'Cancelled';
        $order->save();

        return back()->with('success', 'Order cancelled successfully.');
    }


}
