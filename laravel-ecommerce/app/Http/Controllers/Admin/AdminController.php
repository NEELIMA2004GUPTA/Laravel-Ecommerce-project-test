<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalSales = Order::where('status', 'Delivered')
            ->get()
            ->sum(function($order){
            $subTotal = $order->items->sum(fn($item) => $item->quantity * $item->price);
            $tax = ($subTotal * 5) / 100;
        return $subTotal + $tax;
    });

        // Most sold products
        $topProducts = OrderItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as qty_sold')
            ->groupBy('product_id')
            ->orderByDesc('qty_sold')
            ->take(5)
            ->get();

        // Recent 5 orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalOrders', 'totalSales', 'topProducts', 'recentOrders'));
    }


    public function getSalesData($range)
    {
        $orders = Order::where('status', 'Delivered');

        if ($range == 'daily') {
            $orders = $orders->selectRaw('DATE(created_at) as label, id')
                         ->whereDate('created_at', '>=', Carbon::now()->subDays(7));
        } 
        elseif ($range == 'weekly') {
            $orders = $orders->selectRaw('YEARWEEK(created_at) as label, id')
                         ->whereDate('created_at', '>=', Carbon::now()->subWeeks(8));
        } 
        elseif ($range == 'monthly') {
            $orders = $orders->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as label, id')
                         ->whereDate('created_at', '>=', Carbon::now()->subMonths(12));
        } 
        else { 
            $orders = $orders->selectRaw('YEAR(created_at) as label, id');
        }

        $orders = $orders->get()->groupBy('label');

        $data = $orders->map(function($group, $label){
            $revenue = $group->sum(function($order){
                $subTotal = $order->items->sum(fn($item) => $item->quantity * $item->price);
                $tax = ($subTotal * 5) / 100;
                return $subTotal + $tax;
            });

            return [
                'label' => $label,
                'revenue' => round($revenue, 2)
            ];
        })->values();

        // Format labels for chart
        $data = $data->map(function($item) use ($range){
            if ($range == 'daily') $item['label'] = Carbon::parse($item['label'])->format('d M');
            if ($range == 'weekly') $item['label'] = "Week " . substr($item['label'], -2);
            if ($range == 'monthly') $item['label'] = Carbon::parse($item['label'] . '-01')->format('M Y');
            return $item;
        });

        return response()->json($data);
    }
}
