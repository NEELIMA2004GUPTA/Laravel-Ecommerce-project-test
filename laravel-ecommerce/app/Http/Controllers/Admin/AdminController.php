<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use App\Models\Coupon;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_blocked', 0)->count(); 
        $blockedUsers = User::where('is_blocked', 1)->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $confirmedOrders = Order::where('status', 'confirmed')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
    

        // Most sold products
        $topProducts = OrderItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as qty_sold')
            ->groupBy('product_id')
            ->orderByDesc('qty_sold')
            ->take(5)
            ->get();

        // Recent 5 orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        $dailyCoupons = Coupon::whereDate('created_at', today())->count();
        $weeklyCoupons = Coupon::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthlyCoupons = Coupon::whereMonth('created_at', now()->month)->count();
        $yearlyCoupons = Coupon::whereYear('created_at', now()->year)->count();

        $activeCoupons = Coupon::where('status', 1)->whereDate('expires_at', '>=', today())->count();
        $inactiveCoupons = Coupon::where('status', 0)->count();
        $expiredCoupons = Coupon::whereDate('expires_at', '<', today())->count();

        return view('admin.dashboard', compact('totalUsers','activeUsers','blockedUsers', 'totalOrders','pendingOrders','confirmedOrders','shippedOrders','deliveredOrders','cancelledOrders', 'topProducts', 'recentOrders','dailyCoupons','weeklyCoupons','monthlyCoupons','yearlyCoupons',
        'activeCoupons','inactiveCoupons','expiredCoupons'));
    }


    public function getSalesData($range)
    {
        $orders = Order::where('status', 'Delivered');

        if ($range == 'daily') {
            $orders = $orders->selectRaw('DATE(updated_at) as label, id')
                         ->whereDate('created_at', '>=', Carbon::now()->subDays(7));
        } 
        elseif ($range == 'weekly') {
            $orders = $orders->selectRaw('YEARWEEK(updated_at) as label, id')
                         ->whereDate('created_at', '>=', Carbon::now()->subWeeks(8));
        } 
        elseif ($range == 'monthly') {
            $orders = $orders->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as label, id')
                         ->whereDate('created_at', '>=', Carbon::now()->subMonths(12));
        } 
        else { 
            $orders = $orders->selectRaw('YEAR(updated_at) as label, id');
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
