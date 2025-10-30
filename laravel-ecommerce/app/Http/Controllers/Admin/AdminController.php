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
        $totalSales = Order::where('status', '!=', 'Cancelled')->sum('total');

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
        $query = Order::where('status', '!=', 'Cancelled');

        if ($range == 'daily') {
            $data = $query->selectRaw('DATE(created_at) as label, SUM(total) as revenue')
                          ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
                          ->groupBy('label')->orderBy('label')->get();

        } elseif ($range == 'weekly') {
            $data = $query->selectRaw('YEARWEEK(created_at) as label, SUM(total) as revenue')
                          ->whereDate('created_at', '>=', Carbon::now()->subWeeks(8))
                          ->groupBy('label')->orderBy('label')->get();

        } elseif ($range == 'monthly') {
            $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as label, SUM(total) as revenue')
                          ->whereDate('created_at', '>=', Carbon::now()->subMonths(12))
                          ->groupBy('label')->orderBy('label')->get();

        } else { 
            $data = $query->selectRaw('YEAR(created_at) as label, SUM(total) as revenue')
                          ->groupBy('label')->orderBy('label')->get();
        }

        // Format labels
        $data->transform(function ($item) use ($range) {
            if ($range == 'daily') $item->label = Carbon::parse($item->label)->format('d M');
            if ($range == 'weekly') $item->label = "Week " . substr($item->label, -2);
            if ($range == 'monthly') $item->label = Carbon::parse($item->label . '-01')->format('M Y');
            return $item;
        });

        return response()->json($data);
    }
}
