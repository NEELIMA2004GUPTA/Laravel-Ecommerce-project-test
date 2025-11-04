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
        $response = $this->actingAs($user)->get('/checkout');
        $response->assertRedirect('/cart');
        $response->assertSessionHas('error', 'Your cart is empty.');
    }

    /** @test */
    public function checkout_page_loads_when_cart_has_items()
    {
        $product = Product::factory()->create(['price' => 100, 'discount' => 0]);

        session()->put('cart', [
            $product->id => [
                'title' => $product->title,
                'price' => 100,
                'qty' => 1,
                'stock' => 5,
            ]
        ]);

        $response = $this->actingAs(User::factory()->create())->get('/checkout');
        $response->assertStatus(200);
        $response->assertViewIs('frontend.checkout');
    }

    /** @test */
    public function user_can_place_an_order()
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $product = Product::factory()->create(['price' => 200, 'discount' => 0, 'stock' => 10]);

        session()->put('cart', [
            $product->id => [
                'title' => $product->title,
                'price' => 200,
                'qty' => 2,
                'stock' => 10,
            ]
        ]);

        $response = $this->actingAs($user)->post(route('place.order', [
            'name' => 'Test User',
            'phone' => '9999999999',
            'address' => 'Test Address',
            'payment_method' => 'COD'
        ]));

        $response->assertRedirect(route('orders'));
        $response->assertSessionHas('success', 'Order placed successfully!');

        $order = Order::first();
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(400, $order->total);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // Stock was reduced
        $this->assertEquals(8, $product->fresh()->stock);

        // Cart is cleared
        $this->assertNull(session('cart'));

        // Admin was notified
        Notification::assertSentTo($admin, NewOrderNotification::class);
    }

    /** @test */
    public function user_can_cancel_their_own_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending'
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user)->post("/orders/{$order->id}/cancel");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Order cancelled successfully.');
        $this->assertEquals('Cancelled', $order->fresh()->status);
        $this->assertEquals(7, $product->fresh()->stock);
    }

    /** @test */
    public function user_cannot_cancel_someone_elses_order()
    {
        $order = Order::factory()->create(['status' => 'Pending']);

        $response = $this->actingAs(User::factory()->create())
            ->post("/orders/{$order->id}/cancel");

        $response->assertStatus(403);
    }

    /** @test */
    public function order_cannot_be_cancelled_if_not_pending_or_shipped()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'Delivered'
        ]);

        $response = $this->actingAs($user)->post("/orders/{$order->id}/cancel");

        $response->assertSessionHas('error', 'This order cannot be cancelled.');
    }
}
