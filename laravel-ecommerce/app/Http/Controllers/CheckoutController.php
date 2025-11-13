<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\OrderItem;
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

        // Calculate subtotal
        $subtotal = array_reduce($cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        // Check if a coupon is applied in session
        $couponData = session()->get('coupon'); // e.g., ['code' => 'ABC123', 'discount' => 500]
        $discount = $couponData['discount'] ?? 0;

        // Final total after discount
        $total = max($subtotal - $discount, 0); // prevent negative totals

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
            'total' => $total, // store total after discount
        ]);

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if($admin){
            $admin->notify(new NewOrderNotification($order));
        }

        // Save order items and reduce stock
        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            Product::where('id', $productId)->decrement('stock', $item['quantity']);
        }

        // Clear cart and coupon session
        session()->forget(['cart', 'coupon']);

        return redirect()->route('orders')->with('success', 'Order placed successfully!');
    }

    public function cancelOrder(Order $order)
    {
        // ensure the logged-in user owns the order
        if ($order->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // allow cancellation only if Pending or Confirmed
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

        return redirect()->route('orders')->with('success', 'Order cancelled successfully.');
    }
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Allow editing only if status is Pending or Confirmed
        if (!in_array($order->status, ['Pending', 'Confirmed'])) {
            return back()->with('error', 'You cannot edit details after the order is shipped.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'payment_method' => 'required|in:COD,UPI,CARD',
        ]);

        $order->update($validated);

        return back()->with('success', 'Order details updated successfully.');
    }

}
