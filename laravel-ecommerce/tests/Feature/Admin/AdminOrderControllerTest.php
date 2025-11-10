<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AdminOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_view_orders_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
        $response->assertViewHas('orders');
    }

    #[Test]
    public function admin_can_view_single_order()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.show');
        $response->assertViewHas('order');
    }

}
