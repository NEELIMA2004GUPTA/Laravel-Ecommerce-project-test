<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\FrontProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/sales-data/{range}', [AdminController::class, 'getSalesData']);

    // Manage users
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users/{user}/toggle-block', [UserController::class, 'toggleBlock'])->name('users.toggle-block');
    Route::post('/users/{user}/change-role', [UserController::class, 'changeRole'])->name('users.changeRole');

    // Categories
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{parent}/subcategories', [CategoryController::class, 'getSubcategories']);

    // Products
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/delete-image', [ProductController::class, 'deleteImage'])->name('products.deleteImage');

    // Manage order status by admin
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Coupons
    Route::resource('coupons', CouponController::class);

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class,'password'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // Check-out
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('place.order');

    Route::post('/apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon');
    Route::get('/remove-coupon', [CouponController::class, 'removeCoupon'])->name('remove.coupon');

});

// Product list routes (for frontend display)
Route::get('/products', [FrontProductController::class, 'products'])->name('products.index');
Route::get('/category/{slug}', [FrontProductController::class, 'category'])->name('products.category');
Route::get('/product/{slug}', [FrontProductController::class, 'show'])->name('product.show');
Route::get('/search', [FrontProductController::class, 'search'])->name('products.search');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/clear-cart', function () {
    session()->forget('cart');
    return 'Cart Cleared Now go add products again.';
});

// Order page for users
Route::get('/orders', function () {
    $orders = Order::where('user_id', Auth::id())->with('items.product')->latest()->get();
    return view('frontend.orders.index', compact('orders'));
})->middleware('auth')->name('orders');

Route::post('/orders/{order}/cancel', [CheckoutController::class, 'cancelOrder'])
    ->middleware('auth')
    ->name('orders.cancel');

require __DIR__.'/auth.php';

    