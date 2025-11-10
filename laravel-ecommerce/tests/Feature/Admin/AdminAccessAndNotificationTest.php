<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AdminAccessAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function non_admin_users_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(403); // Because admin middleware should block
    }

    #[Test]
    public function admin_user_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    #[Test]
    public function notification_is_created_when_new_order_is_placed()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Create order (order factory required)
        $order = Order::factory()->create([
            'user_id' => $customer->id
        ]);

        // Trigger notification manually (your controller likely does this)
        $admin->notify(new \App\Notifications\NewOrderNotification($order));

        Notification::assertSentTo(
            [$admin],
            \App\Notifications\NewOrderNotification::class
        );
    }

    #[Test]
    public function admin_can_view_notifications_and_they_get_marked_as_read()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create();

        // Create unread notification
        $admin->notify(new \App\Notifications\NewOrderNotification($order));

        $this->actingAs($admin);

        $response = $this->get('/admin/notifications');

        $response->assertStatus(200);

        // Ensure unread notifications are now marked as read
        $this->assertCount(0, $admin->fresh()->unreadNotifications);
    }
}

