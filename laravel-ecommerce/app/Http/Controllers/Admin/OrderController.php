<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmedMail;

class OrderController extends Controller
{
    public function index(Request $request){
        $search = $request->search;
        $status = $request->status;

        $orders = Order::with('user')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.orders.index', compact('orders', 'search', 'status'));
    }

    public function show(Order $order){
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order){
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Shipped,Delivered,Cancelled'
        ]);

        $order->status = $request->status;
        $order->save();

        // Send mail when order get confirmed
        if ($order->status == 'Confirmed') {
            Mail::to($order->user->email)->send(new OrderConfirmedMail($order));
        }

        return redirect()->back()->with('success', 'Order updated successfully!');
    }
}
