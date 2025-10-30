<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons=Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'discount' => 'required|numeric|min:1',
            'min_amount' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'status' => 'nullable'
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount' => $request->discount,
            'min_amount' => $request->min_amount,
            'expires_at' => $request->expires_at,
            'status' => $request->status ? 1 : 0
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric|min:1',
            'min_amount' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
            'status' => 'nullable'
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount' => $request->discount,
            'min_amount' => $request->min_amount,
            'expires_at' => $request->expires_at,
            'status' => $request->status ? 1 : 0
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Deleted Successfully!');
    }

    public function applyCoupon(Request $request){

        $coupon = Coupon::where('code', $request->coupon_code)
        ->where('status', 1)
        ->first();

        if (!$coupon)
            return back()->with('error', 'Invalid Coupon Code.');

        $cartTotal = collect(session('cart', []))->sum(fn($item) => $item['price'] * $item['qty']);

        if ($cartTotal < $coupon->min_amount)
            return back()->with('error', "Minimum order amount is ₹{$coupon->min_amount} to use this coupon.");

        if ($coupon->expires_at && $coupon->expires_at < now())
            return back()->with('error', 'This coupon has expired.');

        session()->put('coupon', [
            'code' => $coupon->code,
            'discount' => $coupon->discount
        ]);

        return back()->with('success', 'Coupon Applied Successfully!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed successfully!');
    }

}
