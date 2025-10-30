<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(){
        $orders=Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index',compact('orders'));
    }

    public function show(Order $order){
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order){
        $request->validate([
            'status' => 'required|in:Pending,Shipped,Delivered,Cancelled'
        ]);

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Order status updated successfully.');
    }
}
