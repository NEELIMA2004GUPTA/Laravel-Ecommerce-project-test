<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }


    /** @test */
    public function dashboard_displays_summary_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        User::factory()->count(3)->create();
        Order::factory()->count(5)->create();
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

    /** @test */
    public function get_sales_data_returns_json()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/sales-data/daily');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['label', 'revenue']
        ]);
    }
}
