<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_adds_a_product_to_the_cart()
    {
        $product = Product::factory()->create([
            'price' => 100,
            'discount' => 10,
            'stock' => 5,
        ]);

        $response = $this->post(route('cart.add', $product->id));
        $response->assertRedirect();

        $cart = session('cart');

        $this->assertArrayHasKey($product->id, $cart);
        $this->assertEquals(1, $cart[$product->id]['qty']);
        $this->assertEquals(90, $cart[$product->id]['price']); // 100 - 10%
    }

    /** @test */
    public function it_increments_quantity_if_added_twice()
    {
        $product = Product::factory()->create(['stock' => 5]);

        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));

        $cart = session('cart');
        $this->assertEquals(2, $cart[$product->id]['qty']);
    }

    /** @test */
    public function it_updates_cart_quantity_but_not_exceed_stock()
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->post(route('cart.add', $product->id));

        // Try setting qty to higher than stock
        $this->post(route('cart.update', $product->id), ['qty' => 10]);

        $cart = session('cart');
        $this->assertEquals(3, $cart[$product->id]['qty']); // capped to stock
    }

    /** @test */
    public function it_removes_item_from_cart()
    {
        $product = Product::factory()->create();

        $this->post(route('cart.add', $product->id));
        $this->get(route('cart.remove', $product->id));

        $cart = session('cart');
        $this->assertArrayNotHasKey($product->id, $cart);
    }

    /** @test */
    public function logged_in_user_gets_logged_in_cart_message()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('cart.add', $product->id));

        $response->assertSessionHas('success', 'Product added to your cart.');
    }

    /** @test */
    public function guest_user_gets_guest_cart_message()
    {
        $product = Product::factory()->create();

        $response = $this->post(route('cart.add', $product->id));

        $response->assertSessionHas('success', 'Product added to cart. Please login to see your cart.');
    }
}
