<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_is_redirected_if_cart_is_empty_on_checkout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/checkout')
            ->assertRedirect('/cart')
            ->assertSessionHas('error', 'Your cart is empty.');
    }

    /** @test */
    public function order_is_created_successfully()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        // Create cart session
        $cart = [
            $product->id => [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 2,
            ]
        ];
        session(['cart' => $cart]);

        $this->actingAs($user)
            ->post(route('place.order', [
                'name' => 'John Doe',
                'country_code' => '+91',
                'phone' => '9876543210',
                'pincode' => '110011',
                'address' => 'Test Address',
                'payment_method' => 'COD',
            ]))
            ->assertRedirect(route('orders'))
            ->assertSessionHas('success', 'Order placed successfully!');

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals('John Doe', $order->name);
        $this->assertEquals('+919876543210', $order->phone);
        $this->assertEquals(200, $order->total);

        // Order items
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Stock updated
        $this->assertEquals(8, $product->fresh()->stock);

        // Notification sent
        Notification::assertSentTo($admin, NewOrderNotification::class);

        // Cart cleared
        $this->assertEmpty(session('cart'));
    }

    /** @test */
    public function user_can_cancel_own_pending_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'Pending']);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user)
            ->post("/orders/{$order->id}/cancel")
            ->assertSessionHas('success', 'Order cancelled successfully.');

        $this->assertEquals('Cancelled', $order->fresh()->status);
        $this->assertEquals(7, $product->fresh()->stock); // stock returned
    }

    /** @test */
    public function user_cannot_cancel_completed_or_cancelled_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'Delivered']);

        $this->actingAs($user)
            ->post("/orders/{$order->id}/cancel")
            ->assertSessionHas('error', 'This order cannot be cancelled.');
    }

    /** @test */
    public function user_cannot_cancel_someone_elses_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->post("/orders/{$order->id}/cancel")
            ->assertStatus(403);
    }
}

