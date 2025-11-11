<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\User;
use App\Notifications\NewOrderNotification;

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
            'country_code' => 'required',
            'phone' => 'required|digits:10',
            'pincode' => 'required|digits:6',
            'address' => 'required',
            'payment_method' => 'required'
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }

        $total = array_reduce($cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        // Combine country code with phone
        $fullPhone = $request->country_code . $request->phone;

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $fullPhone,                
            'pincode' => $request->pincode,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes ?? null,
            'total' => $total,
        ]);

        $admin = User::where('role', 'admin')->first();
        if($admin){
            $admin->notify(new NewOrderNotification($order));
        }

        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

        // Reduce stock
        Product::where('id', $productId)->decrement('stock', $item['quantity']);
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
        if (!in_array($order->status, ['Pending', 'Confirmed'])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        // Return stock
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }



        // Update status
        $order->status = 'Cancelled';
        $order->save();

        return back()->with('success', 'Order cancelled successfully.');
    }


}
