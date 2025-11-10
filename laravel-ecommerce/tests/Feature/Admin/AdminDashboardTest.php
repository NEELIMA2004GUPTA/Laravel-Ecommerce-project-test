<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function only_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    #[Test]
    public function dashboard_displays_summary_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        User::factory()->count(2)->create();
        Order::factory()->count(3)->create();
        Coupon::factory()->count(2)->create();

        $response = $this->get('/admin/dashboard');

        $response->assertViewHas([
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'totalOrders',
            'pendingOrders',
            'confirmedOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'topProducts',
            'recentOrders',
            'dailyCoupons',
            'weeklyCoupons',
            'monthlyCoupons',
            'yearlyCoupons',
            'activeCoupons',
            'inactiveCoupons',
            'expiredCoupons'
        ]);
    }

    #[Test]
    public function get_sales_data_returns_json_for_all_ranges()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $product = Product::factory()->create();
        $order = Order::factory()->create(['status' => 'Delivered']);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50,
        ]);

        foreach (['daily', 'weekly', 'monthly', 'yearly'] as $range) {
            $response = $this->get("/admin/sales-data/{$range}");
            $response->assertStatus(200);
            $response->assertJsonStructure([ '*' => ['label','revenue'] ]);
        }
    }

    #[Test]
    public function dashboard_calculates_coupon_counts_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

    Coupon::factory()->create(['status' => 1, 'expires_at' => now()->addDay(), 'created_at' => now()]);
    Coupon::factory()->create(['status' => 0, 'expires_at' => now()->subDay(), 'created_at' => now()->subDays(2)]); // same week

        $response = $this->get('/admin/dashboard');

        $response->assertViewHas('activeCoupons', 1);
        $response->assertViewHas('inactiveCoupons', 1);
        $response->assertViewHas('expiredCoupons', 1);
        $response->assertViewHas('dailyCoupons', 1);
        $response->assertViewHas('weeklyCoupons', 1);
    }

    #[Test]
    public function dashboard_counts_orders_by_status_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Order::factory()->create(['status' => 'pending']);
        Order::factory()->create(['status' => 'confirmed']);
        Order::factory()->create(['status' => 'shipped']);
        Order::factory()->create(['status' => 'delivered']);
        Order::factory()->create(['status' => 'cancelled']);

        $response = $this->get('/admin/dashboard');

        $response->assertViewHas('pendingOrders', 1);
        $response->assertViewHas('confirmedOrders', 1);
        $response->assertViewHas('shippedOrders', 1);
        $response->assertViewHas('deliveredOrders', 1);
        $response->assertViewHas('cancelledOrders', 1);
        $response->assertViewHas('totalOrders', 5);
    }
}
