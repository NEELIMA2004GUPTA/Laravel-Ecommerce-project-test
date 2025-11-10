<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MyDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]    
    public function it_redirects_guests_from_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    #[Test]    
    public function authenticated_user_can_see_dashboard_with_correct_order_counts()
    {
        $user = User::factory()->create();

        // Create orders for the user
        Order::factory()->create(['user_id' => $user->id, 'status' => 'Delivered']);
        Order::factory()->create(['user_id' => $user->id, 'status' => 'Pending']);
        Order::factory()->create(['user_id' => $user->id, 'status' => 'Shipped']);
        Order::factory()->create(['user_id' => $user->id, 'status' => 'Cancelled']);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHasAll([
            'totalOrders',
            'delivered',
            'pending',
            'shipped',
            'cancelled'
        ]);

        $response->assertViewHas('totalOrders', 4);
        $response->assertViewHas('delivered', 1);
        $response->assertViewHas('pending', 1);
        $response->assertViewHas('shipped', 1);
        $response->assertViewHas('cancelled', 1);
    }
}
