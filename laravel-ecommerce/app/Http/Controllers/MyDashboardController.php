<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $totalOrders = $user->orders()->count();
        $delivered = $user->orders()->where('status', 'Delivered')->count();
        $pending = $user->orders()->where('status', 'Pending')->count();
        $shipped = $user->orders()->where('status', 'Shipped')->count();
        $cancelled = $user->orders()->where('status', 'Cancelled')->count();
        

        return view('dashboard', compact('totalOrders', 'delivered', 'pending','shipped', 'cancelled'));
    }
}
